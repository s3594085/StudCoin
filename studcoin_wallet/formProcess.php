<?php
	session_start();
	//validate the form 


	$postdata = http_build_query(
			array(
				'nodes' => 'node0',
				'signature' => 'av123',
				'message' => '12 studs',
				'publickey' => '123',
				'recipient' => '1212',
				'amount' => '12'
			)		
	);		

	$opts=array('http' =>
		 array(
	        'method'  => 'POST',
	        'header'  => 'Content-type: application/x-www-form-urlencoded',
	        'content' => $postdata
   		 )

	);
	$context  = stream_context_create($opts);
	$server = '127.0.0.1';
	$port = '5000';
	$url = 'http://'.$server.':'.$port.'/transactions/new';
	$result = file_get_contents('$url', false, $context); 
	print $result; 




	//call add transaction to blockchain API 
	$server = '127.0.0.1';
	$port = '5000';
	$url = 'http://'.$server.':'.$port.'/mine';
	
	//change db  
 	// 
 	include('storeData.php'); 
	updateStatusValue($_SESSION['cart']['itemID']); 
	header("location:account.php");
?>
