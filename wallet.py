def getBalance(address, unspentTxOuts):
    unspentTxOuts = list(filter((lambda x: x.address == address), unspentTxOuts))

    return sum(map(lambda x: x.amount, unspentTxOuts))
