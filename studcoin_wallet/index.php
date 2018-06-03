<?php session_start();?>
<!doctype html>
<html lang="en">
<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<?php include('getUTXO.php');
  

?>
<body>
    <div class="lll">
        <h1>Welcome <?php echo htmlspecialchars($_SESSION['username']);?></h1>
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

      
      
    </script>
</body>
</html>