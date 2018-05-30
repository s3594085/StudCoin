<?php session_start();?>
<!doctype html>
<!--PrivateKey: 654258b7a4e5b29fe49dfa2f95b109a427a222868f44df3a0f866ed6d42f313d-->
<!--PublicKey: fb066f5451334bddaf1a216a9feb1918f103467f7ae2ff6eb8a4b138d7315387feca243e705ca600ce49a4dd3a88ba4f28d0b45e0f2de44cb2c396599bd23524-->
<html lang="en">

<?php include('include/head.inc.php');?>
<link rel="stylesheet" type="text/css" href="css/payment.css">
<?php include('include/nav.inc.php');?>
<body>
    <div class ="row container">
      <div class ="col-md-6"> 
          <div id="item">
              <?php
                include('storeData.php'); 
                getItem($_GET['requestedItemID']);  
                $_SESSION['cart']['itemID'] = $_GET['requestedItemID'];  
              ?>   
          </div>
      </div>
    <div class="col-md-6">
    <form id="userForm" action="formProcess.php" method="post">
      <fieldset>
          <div id='keysForm'>
                  <p>Your Private Key</p>
                  <input type="text" name="privateKey"style="width:450px;"value="<?php echo $_SESSION['privateKey'];?>">
                  <input type="file">
                  <p>Public Key</p> 
                  <input type="text" name="publicKey"style="width:1000px;" value="<?php echo $_SESSION['publicKey'];?>"readonly> 
          </div> 
      </fieldset>
      <fieldset>
          <div id='addressForm'>
              <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    
      
                    <!-- Form Name -->
                    <legend>Address Details</legend>
          
                    <!-- Text input-->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="textinput">Line 1</label>
                      <div class="col-sm-10">
                        <input name="line1" type="text" placeholder="Address Line 1" class="form-control">
                      </div>
                    </div>
          
                    <!-- Text input-->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="textinput">Line 2</label>
                      <div class="col-sm-10">
                        <input name="line2" type="text" placeholder="Address Line 2" class="form-control">
                      </div>
                    </div>
      
                    <!-- Text input-->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="textinput">City</label>
                      <div class="col-sm-10">
                        <input name="city" type="text" placeholder="City" class="form-control">
                      </div>
                    </div>
          
                    <!-- Text input-->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="textinput">State</label>
                      <div class="col-sm-4">
                        <input name="state" type="text" placeholder="State" class="form-control">
                      </div>
          
                      <label class="col-sm-2 control-label" for="textinput">Postcode</label>
                      <div class="col-sm-4">
                        <input name="postcode" type="text" placeholder="Post Code" class="form-control">
                      </div>
                    </div>
      
                    <!-- Text input-->
                    <div class="form-group">
                      <label class="col-sm-2 control-label" for="textinput">Country</label>
                      <div class="col-sm-10">
                        <input name="country" type="text" placeholder="Country" class="form-control">
                      </div>
                    </div>
          </div><!-- /.col-lg-12 -->
          </div><!-- /.row -->
          </div>
          </fieldset>
          <div class="form-group">
              <div class="col-sm-offset-2 col-sm-10">
                <div class="pull-right">
                  <button type="submit" class="btn btn-default">Cancel</button>
                  <button type="submit" class="btn btn-success"">Checkout</button>
                  <input type="button" name="next" class="next" value="Next" />
                </div>
              </div>
          </div>
        </form> 
        </div>   
    </div>
  </div>
        
      
       
            
            
        
        
        
        
<script>
  function purchaseItem(){
      $.post("storeData.php",{updateStatusValue:'true', requestedItemID: <?echo $_GET['requestedItemID']?>});
      window.location.replace("account.php"); 



  }
  var currentFieldset, nextFieldset, previousFieldset; 
  var animating; 
  $(".next").click(function(){
    $(this).parent().hide();
    alert(('.....'));
    if(animating) return false; 
    animating = true; 
    
    currentFieldset = $(this).parent();
    nextFieldset = $(this).parent().next(); 
    
    nextFieldset.show(); 
    currentFieldset.hide(); 
    animating = false;  
      
      
      
      
      
    
    
    
  })
  
  
</script>    
</body>
</html>
<!--https://bootsnipp.com/snippets/featured/address-details-modal-form-->