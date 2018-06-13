<?php

function getUTXO($publicKey){
    $postdata =
      array(
        'publickey' => $publicKey
      );


    $opts=array('http' =>
       array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => json_encode($postdata)
         )
    );
    $result = makeRequest($opts, 'getBalance');
    $balance = json_decode($result, true);
    return $balance['balance'];
}






?>
