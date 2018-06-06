import hashlib
import json
import requests

from threading import Thread
from time import time
from textwrap import dedent
from uuid import uuid4
from flask import Flask, jsonify, request
from urllib.parse import urlparse
from ecdsa import SigningKey, VerifyingKey, NIST256p, SECP256k1
from hashlib import sha256
from wallet import createTransaction, getBalance
from transaction import getCoinbaseTransaction

class Blockchain(object):
    minedBlock = None
    # minerSemaphore
    interrupted = 0
    thread1 = None

    def __init__(self):
        self.chain = []
        self.current_transactions = []
        self.utxo = dict()

        # Creates the genesis block
        block = {
            'index': len(self.chain) + 1,
            'timestamp': time(),
            'previous_hash': 0,
            'difficulty': 0,
            'nonce': 1,
            'transactions': self.current_transactions,
        }
        self.new_block(block)

        self.nodes = set()

    def new_block(self, block):
        # Creates a new block and adds it to the chain

        """
        Creates a new block in the blockchain

        :param block: <dict> The block to be added to the chain
        :return: <dict> New Block
        """

        # Reset the current list of transactions
        self.current_transactions = []

        self.chain.append(block)

        return block

    def new_transaction(self, tx):
        """
        Creates a new transaction to go into the next mined block

        :param sender: <str> Address of the sender
        :param recipient: <str> Address of the recipient
        :param amount: <int> Amount
        :return <int> The index of the block which will contain the transaction
        """
        if tx not in self.current_transactions:
            self.current_transactions.append(tx)

        return self.last_block['index'] + 1

    @staticmethod
    def hash(block):
        """
        Creates a Sha-256 hash of a block
        :param block: <dict> Block
        :return: <str>
        """

        block_string = json.dumps(block, sort_keys=True).encode()
        return hashlib.sha256(block_string).hexdigest()

    @property
    def last_block(self):
        # Returns the last block in the chain
        return self.chain[-1]

    def proof_of_work(self, block):
        """
        Simple proof of work algorithm:
        - Find a number p' such that hash(pp') contains 4 leading zeros, where p is the previous p'
        - p is the previous proof and p' is the new proof
        :param block: <dict> Block to be hashed
        :return: <int>
        """
        guess_hash = self.valid_proof(block)
        while guess_hash[:blockchain.get_difficulty()] != blockchain.get_difficulty() * "0":
            # We have updated our blockchain as another node mined a block, return none and begin mining new block
            if blockchain.interrupted == 1:
                blockchain.interrupted = 0
                return None
            block['nonce'] += 1
            if block['nonce'] > 0x100000000:
                return None
            guess_hash = self.valid_proof(block)
        return block

    "By Seconds"
    BLOCK_GENERATION_INTERVAL = 60
    "By Blocks"
    DIFFICULTY_ADJUSTMENT_INTERVAL = 10

    """
    Obtain difficulty from last block
    """

    def get_difficulty(self):
        latest_block = self.chain[-1]
        if latest_block['index'] % blockchain.DIFFICULTY_ADJUSTMENT_INTERVAL == 0 and latest_block['index'] != 0:
            return blockchain.get_adjusted_difficulty(latest_block, self.chain)
        else:
            return latest_block['difficulty']

    def get_adjusted_difficulty(self, latest_block, block):
        prev_adjustment_block = self.chain[-blockchain.DIFFICULTY_ADJUSTMENT_INTERVAL]
        time_expected = blockchain.BLOCK_GENERATION_INTERVAL * blockchain.DIFFICULTY_ADJUSTMENT_INTERVAL
        time_taken = latest_block['timestamp'] - prev_adjustment_block['timestamp']
        if time_taken < time_expected / 2:
            return prev_adjustment_block['difficulty'] + 1
        elif time_taken < time_expected * 2:
            return prev_adjustment_block['difficulty'] - 1
        else:
            return prev_adjustment_block['difficulty']

    @staticmethod
    def valid_proof(block):
        """
        Validates the proof: Does hash(last_proof, proof) contain 4 leading zeros?

        :param block: <dict> Block to be hashed
        :return: <bool> True if correct, false if not
        """

        guess = json.dumps(block, sort_keys=True).encode()
        guess_hash = hashlib.sha256(guess).hexdigest()
        return guess_hash

    def register_node(self, address, request_back):
        """

        Add a node to the list of nodes
        :param address: <str> Address of the node. Eg. 'http://192.168.0.5:5000'
        :param request_back: <int> if set to 1, we ask the other node to register back. Otherwise just add to our list
        :return: none
        """

        if request_back == 1:
            data = {
                "nodes": [f"http://{host}:{port}/"],
                "request_back": 0
            }
            headers = {'Content-Type': 'application/json'}
            r = requests.post(f'{address}nodes/register', data=json.dumps(data), headers=headers)
            print(r.json())

        self.nodes.add(address)

    def valid_chain(self, chain):
        """

        Determine if a given blockchain is valid

        :param chain: <list> a blockchain
        :return: <bool> True if valid, False if not
        """

        # PROBLEM: Someone could easily create a perfect new chain which has valid proofs
        # and totally different transactions
        last_block = chain[0]
        current_index = 1

        while current_index < len(chain):
            block = chain[current_index]
            print(f'{last_block}')
            print(f'{block}')
            print("\n-----------\n")
            # Check that the hash of the block is correct

            if block['previous_hash'] != self.hash(last_block):
                return False

            # Check that the proof of work is correct
            if not self.valid_proof(block):
                return False

            last_block = block
            current_index += 1

        return True

    def resolve_conflicts(self, length, skip_nodes):
        """
        This is our consensus algorithm, it resolves conflicts by
        replacing our chain with the longest one in the network

        :return: <bool> True if our chain was replaced, False if not
        """

        new_chain = None

        # We are only looking for chains longer than ours
        max_length = len(self.chain)
        if length <= max_length:
            return False

        for node in blockchain.nodes:
            response = requests.get(f'{node}chain')

            if response.status_code == 200:
                length = response.json()['length']
                chain = response.json()['chain']

                # Check if the length is longer and the chain is valid
                if length > max_length and self.valid_chain(chain):
                    max_length = length
                    new_chain = chain
                    utxo_response = requests.get(f'{node}getUTXO')
                    if utxo_response.status_code == 200:
                        blockchain.utxo = utxo_response.json()['utxo']

        if new_chain:
            self.remove_mined_tx(new_chain)
            self.recover_orphaned_tx(new_chain)
            self.chain = new_chain
            nodes_toCall = set(self.nodes) - set(skip_nodes)
            headers = {'Content-Type': 'application/json'}
            data = {
                "length": length,
                "nodes": list(set(skip_nodes) | self.nodes),
            }
            for node in nodes_toCall:
                r = requests.post(f'{node}nodes/resolve', data=json.dumps(data), headers=headers)
                self.interrupted = 1
            return True

        return False

    def remove_mined_tx(self, new_chain):
        new_length = len(new_chain) - 1

        print("HEEEEEEEEEEERRRRRRRRRE")
        print (new_length, " ", len(self.chain))
        while new_length != len(self.chain) - 1:
            txs = new_chain[new_length]['transactions']
            for tx in txs:
                print(tx)
                if tx in blockchain.current_transactions:
                    blockchain.current_transactions.remove(tx)
            new_length -= 1

    def recover_orphaned_tx(self, new_chain):
        our_length = len(self.chain) - 1

        first_index_orphaned_blocked = None

        our_block = self.chain[our_length]
        new_block = new_chain[our_length]

        # If our last block e.g chain[-1] is not in the new chain, it is an orphaned block and
        # we need to recover the transactions out of it and check if they have been processed in the new block
        while our_block != new_block:
            first_index_orphaned_blocked = our_length
            our_length -= 1
            if our_length < 0:
                break
            our_block = self.chain[our_length]
            new_block = new_chain[our_length]

        if first_index_orphaned_blocked is None:
            return

        new_txs = []
        orphaned_txs = []

        for x in range(first_index_orphaned_blocked, len(new_chain)):
            txs = new_chain[first_index_orphaned_blocked]['transactions']
            for tx in txs:
                new_txs.append(tx)

        for x in range(first_index_orphaned_blocked, len(self.chain)):
            our_txs = self.chain[first_index_orphaned_blocked]['transactions']
            for tx in our_txs:
                if tx not in new_txs:
                    orphaned_txs.append(tx)
            first_index_orphaned_blocked += 1

        for tx in orphaned_txs:
            self.current_transactions.append(tx)

        return

    def verify(self, signature, message, publickey):
        try:
            signatureHex = bytes.fromhex(signature)
            #messageString = str(message['amount']) + message['recipient'] + str(message['itemID']) + "04" + message['publickey']
           # messageEncode = messageString.encode()
            #messageString = json.dumps(message, sort_keys=True).encode()
            #print(messageString)
            #demomsg = message['string'].encode()

            publicKeySig = VerifyingKey.from_string(bytes.fromhex(publickey), curve=SECP256k1, hashfunc=hashlib.sha256)
            return publicKeySig.verify(signatureHex , "aaa".encode(), hashlib.sha256)

        except AssertionError:
            print('invalid key')
            return False


# Instantiate our node
app = Flask(__name__)

# Generate a globally unique address for this node
node_identifier = "000116e05a02f0f2b553c041e060ac036b8ebaa1dde1da711b9f6db6c70a6db1b6f50e940246e7e28f908477da6ec982cad2c744610550b65617a19d8fa328b9"  # str(uuid4()).replace('-', '')

# Instantiate our blockchain
blockchain = Blockchain()

@app.route('/nodes/register', methods=['POST'])
def register_nodes():
    values = request.get_json()

    nodes = values.get('nodes')
    request_back = values.get('request_back')
    if nodes is None:
        return "Error: Please supply a valid list of nodes", 400

    for node in nodes:
        blockchain.register_node(node, request_back)

    response = {
        'message': 'New nodes have been added',
        'total_nodes': list(blockchain.nodes),
    }

    return jsonify(response), 201


@app.route('/nodes/resolve', methods=['POST'])
def consensus():
    values = request.get_json()
    length = values.get('length')
    skip_nodes = values.get('nodes')

    if length is None:
        return jsonify(response={
            'message': "No Length specified",
            'chain': blockchain.chain
        }), 200
    replaced = blockchain.resolve_conflicts(length, skip_nodes)

    if replaced:
        blockchain.interrupted = 1
        response = {
            'message': 'Our chain was replaced',
            'new_chain': blockchain.chain,
        }
    else:
        response = {
            'message': "Our chain is authoritative",
            'chain': blockchain.chain
        }

    return jsonify(response),

@app.route('/getBalance', methods=['POST'])
def balance():
    values = request.get_json()
    publickey = values['publickey']
    balance = getBalance(publickey, blockchain.utxo)

    response = {
        "balance": balance
    }

    return jsonify(response), 200



@app.route('/mine', methods=['GET'])
def mine():
    # Miners reward transaction
    blockchain.new_transaction(getCoinbaseTransaction(node_identifier, len(blockchain.chain) + 1).toJSON())

    # We run the proof of work algorithm to get the next proof
    while True:
        last_block = blockchain.last_block
        previous_hash = blockchain.hash(last_block)
        block = {
            'index': len(blockchain.chain) + 1,
            'timestamp': time(),
            'previous_hash': previous_hash or blockchain.hash(blockchain.chain[-1]),
            'difficulty': blockchain.get_difficulty(),
            'nonce': 0,
            'transactions': blockchain.current_transactions,
        }

        block = blockchain.proof_of_work(block)

        if block is None:
            continue

        for tx in block['transactions']:
            for txout in tx['txOuts']:
                if txout['address'] == node_identifier:
                    if node_identifier not in blockchain.utxo:
                        blockchain.utxo[node_identifier] = list()
                    blockchain.utxo[node_identifier].append(txout)

        # Forge the new block by adding it to the chain
        blockchain.new_block(block)

        # We must receive a reward for finding the proof.
        # The sender is 0 to signify that this node has mined a new coin
        # This gets added to the next block as to not change the current block

        response = {
            'message': "New Block Forged",
            'index': block['index'],
            'timestamp': block['timestamp'],
            'previous_hash': block['previous_hash'],
            'difficulty': block['difficulty'],
            'nonce': block['nonce'],
            'transactions': block['transactions'],
        }

        # Update all other nodes
        skip_nodes = list(blockchain.nodes)
        skip_nodes.append(f'http://{host}:{port}/')
        data = {
            "length": len(blockchain.chain),
            "nodes": skip_nodes
        }
        headers = {'Content-Type': 'application/json'}
        for node in blockchain.nodes:
            r = requests.post(f'{node}nodes/resolve', data=json.dumps(data), headers=headers)

        blockchain.minedBlock = None
        break
    return jsonify(block)

@app.route('/buyCoins', methods=['POST'])
def buy_coins():
    values = request.get_json()
    amount = values['amount']
    publickey = values['publickey']

    if getBalance(node_identifier, blockchain.utxo) > amount:
        createTransaction(publickey, amount, node_identifier, blockchain.utxo)
        return 200
    else:
        return 'Node does not have enough funds, try another node', 401

@app.route('/transactions/new', methods=['POST'])
def new_transaction():
    values = request.get_json()
    skip_nodes = set(values.get('nodes'))
    skip_nodes.add(f'http://{host}:{port}/')

    # Check that all required fields are present
    required = ['nodes', 'signature', 'message']
    if not all(k in values for k in required):
        return 'Missing values', 401


    message = values['message']
    publickey = message['publickey']
    recipient = message['recipient']
    amount = message['amount']
    print(message)
    print(values['signature'])

    if not isinstance(amount, int):
        return 'invalid amount', 403

    if amount <= 0:
        return 'Amount cannot be negative', 404

    if not blockchain.verify(values['signature'], message, publickey):
        return 'Invalid Signature', 401

    if getBalance(publickey, blockchain.utxo) < amount:
        return 'Insufficient Balance', 402

    tx = createTransaction(recipient, amount, publickey, blockchain.utxo).toJSON()

    # Creates a new transaction
    index = blockchain.new_transaction(tx)

    for txin in tx['txIns']:
        blockchain.utxo[publickey].remove(txin)

    for txout in tx['txOuts']:
        address = txout['address']
        if address not in blockchain.utxo:
            blockchain.utxo[address] = list()
        blockchain.utxo[address].append(txout)

    headers = {'Content-Type': 'application/json'}
    nodes_to_call = blockchain.nodes - set(skip_nodes)
    data = {
        "signature": values['signature'],
        "nodes": list(set(skip_nodes) | blockchain.nodes),
        "message": values['message'],
    }
    for node in nodes_to_call:
        r = requests.post(f'{node}transactions/new', data=json.dumps(data), headers=headers)

    response = {'message': f'Transaction will be added to Block {index}'}

    return jsonify(response), 500


@app.route('/chain', methods=['GET'])
def full_chain():
    response = {
        'chain': blockchain.chain,
        'length': len(blockchain.chain),
    }
    return jsonify(response), 200


@app.route('/getUTXO', methods=['GET'])
def get_UTXO():
    response = {
        'utxo': blockchain.utxo
    }
    return jsonify(response), 200

@app.route('/generateKeyPair', methods=['GET'])
def generateKeyPair():
    privateKey = SigningKey.generate(curve=NIST256p, hashfunc=sha256)
    publicKey = privateKey.get_verifying_key()
    response = {
        'privateKey': privateKey.to_string().hex(),
        'publicKey' : publicKey.to_string().hex(),
    }
    return jsonify(response)

if __name__ == '__main__':
    host = '0.0.0.0'
    port = 5000
    app.run(host, port, threaded=True)

