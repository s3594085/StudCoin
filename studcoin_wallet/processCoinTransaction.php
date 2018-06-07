<?php
session_start();
$requestedAmount = (int)$_POST['amount'];
$postdata =
    array(
      'amount'=> $requestedAmount,
      'publickey' => $_SESSION['publicKey']
    );



$opts=array('http' =>
   array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => json_encode($postdata)
     )
);
$context  = stream_context_create($opts);
$server = '127.0.0.1';
$port = '5000';
$url = 'http://'.$server.':'.$port.'/buyCoins';

$result = file_get_contents($url, false, $context);
$balanceToAdd = json_decode($result);

include('getUTXO.php');

$_SESSION['utxo'] = getUTXO($_SESSION['publicKey']);


?>
