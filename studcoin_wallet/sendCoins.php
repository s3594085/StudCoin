<?php
session_start();
?>
<!doctype html>
<html lang="en">
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
              <h1>StudCoin Wallet</h1>
              </br></br>
          </div>
<div class="verified" style="padding-left:50px;padding-right:50px;">
    <span style="float:left;font-size:20px">Balance: </span>
    <span id="balance" style="float:right;font-size:20px">0</span>
    </br>
    <hr>
    <h5>Send Transaction</h5>
    <input type="text" id="recipient" class="fadeIn second" name="recipient" placeholder="Recipient Public Address">
    <input type="text" id="amount" class="fadeIn second" name="public" placeholder="Amount to send">
    </br></br>
    <input id="send" type="submit" class="fadeIn second" value="Send">
    <h5>Or</h5>
     <input id="shopping" type="submit" class="fadeIn second" value="Go Shopping">
</div>
</div>
</div>
</body>
</body>
<script>

$("#send").click(function(){
    var recipient = $("#recipient").val();
    var amount = $("#amount").val();

    if (recipient == "" || recipient == null || amount == "" || amount == null) {
        alert("Please enter recipient & amount!");
    } else {
        var message = {
            pubKey: pubKey,
            amount: amount,
            recipient: recipient
        };

        var signature = sign(message);

        var pack = {
            signature: signature,
            message: message
        };

        pack = JSON.stringify(pack);

        console.log(pack);

        $('.sent').show();
        $('.verified').hide();
    }
});s

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
