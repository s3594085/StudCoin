<?php


function makeRequest($options, $urlMethod){
    $context = stream_context_create($options);
    $server ='127.0.0.1';
    $port = '5000';
    $url = 'http://'.$server.':'.$port.'/'.$urlMethod;
    $result = @file_get_contents($url, false, $context);
    return $result;
}


?>
