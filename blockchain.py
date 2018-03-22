import hashlib
import json
import requests

from threading import Thread
from time import time
from textwrap import dedent
from uuid import uuid4
from flask import Flask, jsonify, request
from urllib.parse import urlparse


class Blockchain(object):

    minedBlock = None
    # minerSemaphore
    interrupted = 0
    thread1 = None

    def __init__(self):
        self.chain = []
        self.current_transactions = []

        # Creates the genesis block
        block = {
            'index': len(self.chain) + 1,
            'timestamp': time(),
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

    def new_transaction(self, sender, recipient, amount):
        """
        Creates a new transaction to go into the next mined block

        :param sender: <str> Address of the sender
        :param recipient: <str> Address of the recipient
        :param amount: <int> Amount
        :return <int> The index of the block which will contain the transaction
        """

        self.current_transactions.append({
            'sender': sender,
            'recipient': recipient,
            'amount': amount,
        })

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

    def resolve_conflicts(self, skip_nodes):
        """
        This is our consensus algorithm, it resolves conflicts by
        replacing our chain with the longest one in the network

        :return: <bool> True if our chain was replaced, False if not
        """

        neighbours = self.nodes
        new_chain = None

        # We are only looking for chains longer than ours
        max_length = len(self.chain)

        for node in neighbours:
            response = requests.get(f'{node}chain')

            if response.status_code == 200:
                length = response.json()['length']
                chain = response.json()['chain']

                # Check if the length is longer and the chain is valid
                if length > max_length and self.valid_chain(chain):
                    """
                    for node in neighbours:
                        if node not in skip_nodes:
                            headers = {'Content-Type': 'application/json'}
                            r = requests.post(f'{node}nodes/register', data=json.dumps(), headers=headers)
                            # Call consenus on nodes
                    """
                    max_length = length
                    new_chain = chain

        if new_chain:
            self.chain = new_chain
            return True, skip_nodes

        return False


# Instantiate our node
app = Flask(__name__)

# Generate a globally unique address for this node
node_identifier = str(uuid4()).replace('-', '')

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
    skip_nodes = values.get('nodes')

    replaced = blockchain.resolve_conflicts(skip_nodes)

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

    return jsonify(response), 200


@app.route('/mine', methods=['GET'])
def mine():
    # Miners reward transaction
    blockchain.new_transaction(
        sender="0",
        recipient=node_identifier,
        amount=1
    )

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

        # Forge the new block by adding it to the chain
        blockchain.new_block(block)

        # We must receive a reward for finding the proof.
        # The sender is 0 to signify that this node has mined a new coin
        # This gets added to the next block as to not change the current block

        response = {
            'message': "New Block Forged",
            'index': block['index'],
            'nonce': block['nonce'],
            'previous_hash': block['previous_hash'],
            'transactions': block['transactions'],
        }

        # Update all other nodes
        nodes = list(blockchain.nodes)
        data = {
            "nodes": nodes
        }
        headers = {'Content-Type': 'application/json'}
        for node in blockchain.nodes:
            r = requests.post(f'{node}nodes/resolve', data=json.dumps(data), headers=headers)

        blockchain.minedBlock = None
        break
    return jsonify(block)


@app.route('/transactions/new', methods=['POST'])
def new_transaction():
    values = request.get_json()

    # Check that all required fields are present
    required = ['sender', 'recipient', 'amount']
    if not all(k in values for k in required):
        return 'Missing values', 400

    # Creates a new transaction
    index = blockchain.new_transaction(values['sender'], values['recipient'], values['amount'])

    response = {'message': f'Transaction will be added to Block {index}'}
    return jsonify(response), 500


@app.route('/chain', methods=['GET'])
def full_chain():
    response = {
        'chain': blockchain.chain,
        'length': len(blockchain.chain),
    }
    return jsonify(response), 200

if __name__ == '__main__':
    host = '127.0.0.1'
    port = 5000
    app.run(host, port, threaded=True)
