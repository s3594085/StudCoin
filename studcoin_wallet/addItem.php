<?php session_start();?>
<!DOCTYPE html>
<html>
    <head>
        <title>StudCoin Wallet</title>

        <link href="css/custom.css" rel="stylesheet">
        <link href="css/bootstrap.min.css" rel="stylesheet">
    <?php include('include/head.inc.php');?>
    </head>
    <?php include('include/nav.inc.php');?>
    <body>
        <div class="wrapper fadeInDown">
            <div id="formContent">
                <!-- Tabs Titles -->

                <!-- Icon -->
                <div class="fadeIn first">
                    </br></br>
                    <h1>Sell Item</h1>
                    </br></br>
                </div>
                <div class="addItems">
                  <input type="text" id="recipient" class="fadeIn second" name="recipient" placeholder="Item Name">
                  <input type="text" id="recipient" class="fadeIn second" name="recipient" placeholder="Desciption and condition of item">
                  <br>$<input type="number" id="amount" width="25%" min="0" max="9999" step="0.01"placeholder="Price of item"/>
                  <br><br>
                  <span>Please upload image</span>
                  <br>
                  <input type="file" name="fileToUpload" id="fileToUpload" style="margin:0 auto; width:46%">
                  </br></br>
                  <input id="sell" type="submit" class="fadeIn second" value="Sell" >
                </div>
            </div>
        </div>
    </body>

<script>
$("#sell").click(function(){
    window.location.replace('index.php');


});

function clear() {
    pubKey = null;
    privKey = null;

    $("#public").val("");
    $("#private").val("");
    $("#recipient").val("");
    $("#amount").val("");
}
</script>

</html>
