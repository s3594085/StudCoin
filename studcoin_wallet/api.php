<?php

$request = "http://" . $_GET['server'] . ":" . $_GET['port'] . "/balance/" . $_GET['pubKey'];

$respond = file_get_contents($request);

print $respond;

?>