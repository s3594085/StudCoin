<?php
	session_start();
	//validate the form 




	//change values in db
	include('storeData.php'); 
	updateStatusValue($_SESSION['cart']['itemID']); 

	//send data to blockchain 
	$itemID =  $_SESSION['cart']['itemID'];
	if(!isset($_POST['message']) || !isset($_POST['signature'])){
		echo('invalid form'); 
		exit; 
	}
	$message = $_POST['message']; 
	$signature = $_POST['signature'];  
	$file_db = new PDO('sqlite:db_test.db');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION); 
    $result = $file_db->query("
        SELECT * FROM items
        WHERE itemID==$itemID
    ");
   	$itemsList = array(); 
        foreach($result as $row){
            $item = array(
                'itemID' => $row['itemID'],
                'amt' => $row['price'],
                'sellerPublicKey' => $row['seller'],
                'itemName' => $row['itemName'],
                'img' => $row['image'],
                'status' => $row['status']
            );   
            array_push($itemsList, $item); 
            
      	}  


	//get the item purchased
	$postdata = http_build_query(
			array(
				'nodes' => 'node0',
				'signature' => $signature,
				'message' => $message,
				'publickey' => $_SESSION['publicKey'],
				'recipient' => $row['seller'],
				'amount' => $row['price']
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
	//$result = file_get_contents('$url', false, $context); 

	//redirect to account page 
	header("location:account.php");
?>
