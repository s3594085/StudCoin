import json
import hashlib

from flask import jsonify
from functools import reduce

COINBASE_AMOUNT = 50


class UnspentTxOut:
    txOutId = None
    txOutIndex = None
    address = None
    amount = None

    def __init__(self, txOutId, txOutIndex, address, amount):
        self.txOutId = txOutId
        self.txOutIndex = txOutIndex
        self.address = address
        self.amount = amount


class TxIn:
    txOutId = None
    txOutIndex = None
    signature = None


class TxOut:
    address = None
    amount = None

    def __init__(self, address, amount):
        self.address = address
        self.amount = amount


class Transaction:
    id = None

    txIns = []
    txOuts = []


def getTransactionId(transaction):
    txInContent = []
    txOutContent = []

    for txIn in transaction.txIns:
        txInContent.append(txIn.txOutId + str(txIn.txOutIndex))

    txInContent = reduce((lambda x, y: x + y), txInContent)

    for txOut in transaction.txOuts:
        txOutContent.append(txOut.address + str(txOut.amount))

    txOutContent = reduce((lambda x, y: x + y), txOutContent)

    string = txInContent + txOutContent

    return hashlib.sha256(string.encode()).hexdigest()


def validateTransaction(transaction, aUnspentTxOuts):
    if getTransactionId(transaction) != transaction.id:
        return False

    return True


def validateBlockTransactions(aTransactions, aUnspentTxOut, blockIndex):
    coinbaseTx = aTransactions[0]

    return False


def validateCoinbaseTx(transaction, blockIndex):
    return False


def validateTxIn(txIn, transaction, aUnspentTxOut):
    referencedUTxOut = None

    for uTxO in aUnspentTxOut:
        if uTxO.txOutId == txIn.txOutId:
            referencedUTxOut = uTxO
            break

    if referencedUTxOut == None:
        return False

    address = referencedUTxOut.address

    # Verify transaction.id with txIn.signature
    # return true if valid

    return False


def getTxInAmount(txIn, aUnspentTxOuts):
    return 0


def findUnspentTxOut(transactionId, index, aUnspentTxOuts):
    return UnspentTxOut


def getCoinbaseTransaction(address, blockIndex):
    t = Transaction()
    txIn = TxIn()
    txIn.signature = ''
    txIn.txOutId = ''
    txIn.txOutIndex = blockIndex

    t.txIns.append(txIn)
    t.txOuts.append(TxOut(address, COINBASE_AMOUNT))
    t.id = getTransactionId(t)

    return t


def signTxIn(transaction, txInIndex, privateKey, aUnspentTxOuts):
    txIn = transaction.txIns[txInIndex]

    dataToSign = transaction.id
    referencedUnspentTxOut = findUnspentTxOut(txIn.txOutId, txIn.txOutIndex, aUnspentTxOuts)

    if referencedUnspentTxOut == None:
        return None

    referencedAddress = referencedUnspentTxOut.address

    """
    if getPublicKey(privateKey) != referencedAddress:
        return None
    """

    return None


