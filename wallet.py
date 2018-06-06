import transaction

count = 0

def getBalance(address, unspentTxOuts):
    if address not in unspentTxOuts:
        return 0

    unspentTxOuts = list(unspentTxOuts[address])
    total = 0
    for txout in unspentTxOuts:
        total += txout["amount"]

    print(total)
    return total


def findTxOutsForAmount(amount, senderUnspentTxOuts):
    currentAmount = 0
    includedUnspentTxOuts = []

    for senderUTxO in senderUnspentTxOuts:
        includedUnspentTxOuts.append(senderUTxO)
        currentAmount += senderUTxO['amount']

        if currentAmount >= amount:
            leftOverAmount = currentAmount - amount
            return includedUnspentTxOuts, leftOverAmount

    print("Insufficient amount to send transaction")

    return None, None


def createTxOuts(txIns, receiverAddress, senderAddress, amount, leftOveramount):
    txOut1 = transaction.TxOut(transaction.generateTxOutID(txIns, receiverAddress, amount), receiverAddress, amount)

    if leftOveramount == 0:
        return [txOut1]
    else:
        leftOverTx = transaction.TxOut(transaction.generateTxOutID(txIns, senderAddress, amount),senderAddress, leftOveramount)
        return [txOut1, leftOverTx]

def createTransaction(receiverAddress, amount, publicKey, unspentTxOuts):
    # Required to turn privateKey to public address
    senderUnspentTxOuts = unspentTxOuts[publicKey]

    if senderUnspentTxOuts:
        includedUnspentTxOuts, leftOverAmount = findTxOutsForAmount(amount, senderUnspentTxOuts)
    else:
        return None

    def toUnsignedTxIn(unspentTxOut):
        print(unspentTxOut)
        txIn = transaction.TxIn(transaction.TxOut(unspentTxOut["id"], unspentTxOut["address"], unspentTxOut["amount"]))
        return txIn

    if includedUnspentTxOuts or leftOverAmount:
        unsignedTxIns = list(map(lambda x: toUnsignedTxIn(x), includedUnspentTxOuts))

        tx = transaction.Transaction()
        tx.txIns = unsignedTxIns
        tx.txOuts = createTxOuts(tx.txIns, receiverAddress, publicKey, amount, leftOverAmount)
        tx.id = transaction.getTransactionId(tx)

        #for index, txIn in enumerate(tx.txIns):
            #txIn.signature = transaction.signTxIn(tx, index, publicKey, unspentTxOuts)

        return tx
    else:
        return None