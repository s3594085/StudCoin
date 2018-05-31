<?php session_start();
var_dump($_SESSION);?>
<!doctype html>
<html lang="en">
<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<?php include('getUTXO.php');
  



  // $_SESSION['publicKey'] = $_GET['publicKey']; 
  // $_SESSION['privateKey'] = $_GET['privateKey']; 
  // $_SESSION['utxo'] = getUTXO($_SESSION['publicKey']); 
 
  // $_SESSION['publicKey'] = '04b82bfffd1548557fe2d90e87806d166070b436ecba4ff5073e06049f4d21662ed7e01c78c52d4176f4436e10d90c567778ed7d8daae1e02fda6456a1a7c5803a';
  // $_SESSION['privateKey'] = 'ce413e9cb157cd9adf96ce99753f5917f3c4058d55a1144c710054ddbf21bc37';
  // $_SESSION['utxo'] = getUTXO($_SESSION['publicKey']); 


?>
<body>
    <div class="lll">
        <h1>Welcome <?php echo htmlspecialchars($_SESSION['utxo']);?></h1>
        <div class="container">
          <div id="itemsList">
          </div>
        </div>

        
        <hr />
        
        <p><small>Thanks for visiting</small></p>
    </div>
    <script>
    window.onload = function(){
          $.ajax({
          'url': 'storeData.php',
          'type': 'POST',
          'data': {loadItems:'true'},
          'success': function(result){
              $('#itemsList').html(result); 
          }
      })
    };

    function add(id){
  
      //$.get('paymentfdfd.php', {requestedItemID: 2});
      // var ele=document.getElementById(id);
      // var name=document.getElementById(id+"_name").value;
      // var price=document.getElementById(id+"_price").value;
      // $.ajax({
      //   type:'get',
      //   url:'payment.php',
      //   data:{
      //     requestedItemID: 2
      //   },
        //success:function(response){
          //document.getElementById("totalItems").innerHTML=response; 
        //}
      // });    
    };  
      
      
      
    </script>
</body>
</html>