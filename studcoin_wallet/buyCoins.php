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
                    <h1>StudCoin Wallet</h1>
                    </br></br>
                </div>

                <div class="BuyCoins" style="padding-left:50px;padding-right:50px;">
                    <span id="errorNotify" style="color:red;"></span><br>
                    <h5>Buy Coins</h5>
                    <input type="text" id="amount" class="fadeIn second" name="public" placeholder="Amount to Buy">
                    </br></br>
                    <input id="requestCoinsSubmit" type="submit" class="fadeIn second" value="Buy">
                </div>

                <div class="verified" style="display:None;padding-left:50px;padding-right:50px;">
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

<script>
/* Global Declaration */


/* Actions */




$("#requestCoinsSubmit").click(function(){
    var enteredAmount = $("#amount").val();
    var positiveNumberRegex = new RegExp("^[1-9]\\d*$");
    if(!positiveNumberRegex.test(enteredAmount)){
      alert('please enter a positive number');
      $('#amount').val('');
      return;
    }
    else{
      $.post('processCoinTransaction.php', {amount: enteredAmount})
        .done(function(data){
              console.log(data);
              alert('balance updated');
              window.location.replace('index.php');
        })
        .fail(function(data){
            console.log(data);
            $("#errorNotify").html('Requested amount is unavailable. Please choose another amount');

        })
        ;
    }
});








$("#shopping").click(function(){
    window.location.replace('index.php');


});

$("#amount").click(function(){
    $("#errorNotify").html("");
    $("#amount").val("");
});
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
