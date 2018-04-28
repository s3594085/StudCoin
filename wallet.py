import transaction


def getBalance(address, unspentTxOuts):
    unspentTxOuts = list(filter((lambda x: x.address == address), unspentTxOuts))

    return sum(map(lambda x: x.amount, unspentTxOuts))


def findTxOutsForAmount(amount, senderUnspentTxOuts):
    currentAmount = 0
    includedUnspentTxOuts = []

    for senderUTxO in senderUnspentTxOuts:
        includedUnspentTxOuts.append(senderUTxO)
        currentAmount += senderUTxO.amount

        if currentAmount >= amount:
            leftOverAmount = currentAmount - amount
            return includedUnspentTxOuts, leftOverAmount

    print("Insufficient amount to send transaction")

    return None, None


def createTxOuts(receiverAddress, senderAddress, amount, leftOveramount):
    txOut1 = transaction.TxOut(receiverAddress, amount)

    if leftOveramount == 0:
        return [txOut1]
    else:
        leftOverTx = transaction.TxOut(senderAddress, leftOveramount)
        return [txOut1, leftOverTx]


def createTransaction(receiverAddress, amount, privateKey, unspentTxOuts):
    # Required to turn privateKey to public address
    senderAddress = "abc"
    senderUnspentTxOuts = list(filter(lambda x: x.address == senderAddress, unspentTxOuts))

    if senderUnspentTxOuts:
        includedUnspentTxOuts, leftOverAmount = findTxOutsForAmount(amount, senderUnspentTxOuts)
    else:
        return None

    def toUnsignedTxIn(unspentTxOut):
        txIn = transaction.TxIn()
        txIn.txOutId = unspentTxOut.txOutId
        txIn.txOutIndex = unspentTxOut.txOutIndex
        return txIn

    if includedUnspentTxOuts or leftOverAmount:
        unsignedTxIns = list(map(lambda x: toUnsignedTxIn(x), includedUnspentTxOuts))

        tx = transaction.Transaction()
        tx.txIns = unsignedTxIns
        tx.txOuts = createTxOuts(receiverAddress, senderAddress, amount, leftOverAmount)
        tx.id = transaction.getTransactionId(tx)

        for index, txIn in enumerate(tx.txIns):
            txIn.signature = transaction.signTxIn(tx, index, privateKey, unspentTxOuts)

        return tx
    else:
        return None
