<?php 

$file_db = new PDO('sqlite:db_test.db');

$file_db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION); 
    




$itemsList = array(); 
$result = $file_db->query("SELECT * FROM items"); 




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

$html .= ' <div class ="row">';
foreach($itemsList as $item){
    
    $html .= '<div class="col-sm-6 col-md-4 col-lg-3 p-b-50">';
    $html .= '<form action="payment.php" method="get">'; 
    $html .= '<div class="block" id="item1">'; 
    $html .= '<div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">';
    $html .= '<img src="' . $item['img'] . '" alt="IMG-PRODUCT"class="img-fluid">'; 
    $html .= ' </div>'; 
    $html .= '<p>' . $item['itemName'] . '</p>';    
    $html .= '<p>' . $item['amt'] . '</p'; 
    $html .= '<input type="hidden" id="item' . $item['itemID'] .'_price" value ="'. $item['amt'].'">'; 
    $html .= '<input type="hidden" id="item' . $item['itemID'] .'_name" value ="'. $item['name'].'">'; 
    $html .= '<button  class="bg-success" type="submit" id="buyButton" onclick="add(' . $item['itemID'] .')">Buy</button>'; 
    $html .= '</div>'; 
    $html .= '</form>'; 
    $html .= '</div>'; 
    
}
$html .= '</div>';     
echo($html);                 
    
?>