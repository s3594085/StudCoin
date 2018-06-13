<?php
session_start();


if(isset($_POST['loadItems'])){
    fetchItems();
}

if(isset($_GET['updateStatusValue'])){
    updateStatusValue($_GET['requestedItemID']);
}

if(isset($_POST['getAllUserPurchases'])){
    getAllUserPurchases($_POST['userID']);
}

if(isset($_POST['getItem'])){
  printModal($_POST['itemID']);
}
function getItem($itemID){
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

      $html .= '<div class="container row">';
          $html .= '<span class ="container col-sm-4">Item</span>';
          $html .= '<span class ="container col-sm-4">Name</span>';
          $html .= '<span class ="container col-sm-4">Price</span>';
          foreach($itemsList as $item){
          $html .='<div class ="container col-sm-4">';
              $html .='<div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">';
                  $html .='<img src="'. $item['img'] .'"alt="IMG-PRODUCT"class="img-fluid">';
              $html .='</div>';
          $html .='</div>';
          $html .='<div class ="container col-sm-4 my-auto">';
                $html .='<span>'. $item['itemName'] .'</span>';
          $html .='</div>';
          $html .='<div class ="container col-sm-4 my-auto">';
                $html .='<span>'. $item['amt'] .'</span>';
          $html .='</div>';
          }
      $html .='</div>';

    echo($html);
}
function getAllUserPurchases($userID){


    $file_db = new PDO('sqlite:db_test.db');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
    $itemsList = array();
    $result = $file_db->query("
        SELECT items.*
        FROM items
        INNER JOIN solditems ON items.itemID == soldItems.itemID
        WHERE soldItems.buyerID=='{$_SESSION['publicKey']}'
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
    $html .= ' <div class ="row">';
    foreach($itemsList as $item){
            $html .= '<div class="col-sm-6 col-md-4 col-lg-3 p-b-50">';
            $html .= '<form action="payment.php" method="get">';
            $html .= '<div class="block mh-100" id="item1">';
            $html .= '<div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">';
            $html .= '<img  src="' . $item['img'] . '" alt="IMG-PRODUCT" width=720 class="img-fluid">';
            $html .= ' </div>';
            $html .= '<div class="row">';
            $html .= '<div class="col-sm">';
            $html .= '<p>' . $item['itemName'] . '</p>';
            $html .= '<p>' . $item['amt'] . '</p>';
            $html .= '</div>';
            $html .= '<input type="hidden" name="requestedItemID" value="' . $item['itemID'] . '">';
            $html .= '<input type="hidden" id="item' . $item['itemID'] . '_price" value ="' . $item['amt'] . '">';
            $html .= '<input type="hidden" id="item' . $item['itemID'] .'_name" value ="'. $item['itemName'].'">';
            $html .= '<div class="col-sm">';
            if($item['status']=='pending'){
                $html .= '<button type="button" class="float-right btn btn-lg btn-success" disabled>Purchased</button>';
            }elseif($item['status']=='purchased'){
                $html .= '<button type="button" class="float-right btn btn-lg btn-success" disabled>Yours</button>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</form>';
            $html .= '</div>';
    }
    $html .= '</div>';
    echo($html);
}

function updateStatusValue($requestedItemID){
    $publicKey = $_SESSION['publicKey'];

    $file_db = new PDO('sqlite:db_test.db');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
    $file_db->exec(
        "UPDATE items
        SET status='pending'
        WHERE itemID == $requestedItemID
    ");
    $file_db->exec(
        "INSERT INTO soldItems(itemID, buyerID, status)
         VALUES ($requestedItemID, '{$_SESSION['publicKey']}', 'pending')
        ");

}

function fetchItems(){
    $file_db = new PDO('sqlite:db_test.db');
    $file_db->setAttribute(PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION);
        $result = $file_db->query("SELECT * FROM items
                    WHERE status=='available'
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
        printItems($itemsList);
}

function printItems($itemsList){
    $html .= ' <div class ="row">';
    foreach($itemsList as $item){
            $html .= '<div class="canClick col-sm-6 col-md-4 col-lg-3 p-b-50" onClick=myFunc('. $item['itemID'].')>';
              $html .= '<form action="payment.php" method="get">';
                $html .= '<div class="block container" id="item1">';
                  $html .= '<div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">';
                      $html .= '<img src="' . $item['img'] . '" alt="IMG-PRODUCT" width="720" height="960" class="img-fluid">';
                  $html .= ' </div>';
                  $html .= '<div class="itemDetails">';
                      $html .= '<p>' . $item['itemName'] . '</p>';
                      $html .= '<div id ="pricePurchase">';
                          $html .= '<span class="container" id="price">' . $item['amt'] . '</span>';
                          $html .= '<input type="hidden" name="requestedItemID" value="' . $item['itemID'] . '">';
                          $html .= '<input type="hidden" name="amt" id="item' . $item['itemID'] . '_price" value ="' . $item['amt'] . '">';
                          $html .= '<input type="hidden" id="item' . $item['itemID'] .'_name" value ="'. $item['itemName'].'">';
                          $html .= '<input type="hidden" name="seller" id="item' . $item['itemID'] .'seller" value ="'. $item['sellerPublicKey'].'">';
                if($item['amt']> $_SESSION['utxo']){
                          $html .= '<button  class="container btn btn-lg bg-danger" type="submit" name="itemName" id="buyButton" disabled>Insufficient<br>funds</button>';
                }else{
                          $html .= '<button  class="container vertical-center btn btn-lg bg-success" type="submit" name="itemName" id="buyButton" onclick="add(' . $item['itemID'] .')">Buy</button>';
                }
                      $html .= '</div>';
                  $html .= '</div>';
                $html .= '</div>';
              $html .= '</form>';
            $html .= '</div>';
    }
    $html .= '</div>';
    echo($html);
}

function printModal($itemID){
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
              'status' => $row['status'],
              'description' => $row['description']

          );
          array_push($itemsList, $item);

      }
  $html .= '<h5 class="modal-title" id="exampleModalLongTitle">'.$item['itemName'].'</h5>';
  $html .= '<form action="payment.php" method="get">';
  $html .= '<div class="container row">';


  foreach($itemsList as $item){
      $html .= '<div class="row">';
          $html .='<div class ="container col-sm-4">';
              $html .='<div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">';
                    $html .='<img src="'. $item['img'] .'"alt="IMG-PRODUCT"class="img-fluid" width="720">';
              $html .='</div>';
          $html .='</div>';
          $html .='<div class ="container col-sm-8">';
              $html .='<span><br>'.$item['description'].'</span>';
          $html .='</div>';
            $html .='</div>';
          $html .='<div class ="col-sm-12">';
                $html .=' <span class="float-right" style="font-weight: bold;font-size: 200%;">'. $item['amt'] .'</span>';
          $html .='</div>';
        $html .='</div>';
  }

  $html .= '<input type="hidden" name="requestedItemID" value="' . $item['itemID'] . '">';
  $html .= '<input type="hidden" name="amt" id="item' . $item['itemID'] . '_price" value ="' . $item['amt'] . '">';
  $html .= '<input type="hidden" id="item' . $item['itemID'] .'_name" value ="'. $item['itemName'].'">';
  $html .= '<input type="hidden" name="seller" id="item' . $item['itemID'] .'seller" value ="'. $item['sellerPublicKey'].'">';
  $html.='<div class="modal-footer">';
  $html.='<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>';
  if($item['amt']> $_SESSION['utxo']){
            $html .= '<button  class="btn bg-success" type="submit" disabled>Buy</button>';
  }else{
            $html .= '<button  class="btn bg-success" type="submit">Buy</button>';
  }
  $html.= '</form';
  $html.= '</div>';
  echo($html);
}

?>
