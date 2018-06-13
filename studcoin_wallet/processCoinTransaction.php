<?php
session_start();
include('getUTXO.php');
include('apicall.php');
$requestedAmount = (int)$_POST['amount'];
$postdata =
    array(
      'amount'=> (int)$requestedAmount,
      'publickey' => $_SESSION['publicKey'],
      'recipient' => $_SESSION['publicKey']
    );

$opts=array('http' =>
   array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/json',
        'content' => json_encode($postdata)
     )
);

$result = makeRequest($opts, 'buyCoins');
if($result===FALSE){
  header("HTTP/1.1 406 Not Acceptable");
  return;
}

//get the balance and update the user balance

$_SESSION['utxo'] = getUTXO($_SESSION['publicKey']);
?>
