<?php session_start();?>
<!doctype html>
<html lang="en">
<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<body>
    <div class="lll">
    <h1>Welcome <?php echo htmlspecialchars($_SESSION['username']);?></h1>
    <div class="container">
      
        <div id="third"></div>
        <form id='itemsForm' action='test.php' method='post'>
          <input='hidden' name='loadItems' value="dasdsa">  
        </form>
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
          'data': $('#itemsForm').serialize(),
          'success': function(result){
              $('#itemsList').html(result); 
          }
      })
    };

      function add(id){
        var ele=document.getElementById(id);
        var name=document.getElementById(id+"_name").value;
        var price=document.getElementById(id+"_price").value;
        $.ajax({
          type:'post',
          url:'payment.php',
          data:{
            item_src: '0',
            item_name:name,
            item_price:price
          },
          //success:function(response){
            //document.getElementById("totalItems").innerHTML=response; 
          //}
        });    
        
        
        
      };  
      
      
      
    </script>
</body>
</html>