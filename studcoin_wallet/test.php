<?php 

	$itemID = 2; 
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

  	var_dump($result['price']); 

?>