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
    $context  = stream_context_create($opts);
    $server = '127.0.0.1';
    $port = '5000';
    $url = 'http://'.$server.':'.$port.'/getBalance';

    $result = file_get_contents($url, false, $context);
    $balance = json_decode($result, true);
    var_dump($balance); 
    return $balance['balance'];
}






?>
