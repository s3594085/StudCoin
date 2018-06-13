<?php session_start();?>
<!doctype html>
<html lang="en">
<script language='JavaScript' type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.4/jsrsasign-all-min.js'></script>
<link rel="stylesheet" type="text/css" href="css/payment.css">
<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<body>
        <div class="container" style="width:50%;">
              <div id="item">
                  <?php
                    //add the item user requested as session variables
                    include('storeData.php');
                    getItem($_GET['requestedItemID']);
                    $_SESSION['cart']['itemID'] = $_GET['requestedItemID'];
                    $_SESSION['cart']['seller'] = $_GET['seller'];
                    $_SESSION['cart']['amt'] = $_GET['amt'];
                  ?>
              </div>
              <hr>
              <div id='keysForm' class="container" style="margin-left:12%;">
                  <br><br><br>
                  <p>Public Key</p>
                  <input type="text" id="publicKey"style="width:450px;" value="<?php echo $_SESSION['publicKey'];?>"readonly>
                  <br><br>
                  <p>Enter Your Private Key</p>
                  <input type="text" id="privateKey"style="width:450px;">
                  <div class="form-group" style="margin-top:10px;">
                      <button type="submit" id="cancel" class="btn btn-default">Cancel</button>
                      <button type="submit" id="checkout" class="btn btn-success">Buy</button>
                  </div>
              </div>
        </div>


<script>
  //Global variables for key pair validation
  // 04 required for key validation solely for the javascript platform
  var pubKey = '04'+'<?php echo($_SESSION['publicKey']);?>';
  var privKey;
  var curve = "NIST P-256";
  var hashFunc = "SHA256withECDSA";

  $("#cancel").click(function(){
      window.location = 'index.php';
  });
  $("#checkout").click(function(){
    privKey = $.trim($('#privateKey').val());
    //validate keypair first
    console.log(pubKey);

    if(verifyKeyPair()){
      var messageRaw = new Object();
      messageRaw.amount = <?php echo $_SESSION['cart']['amt'];?>;
      messageRaw.recipient = '<?php echo $_SESSION['cart']['seller'];?>';
      messageRaw.itemID = <?php echo $_SESSION['cart']['itemID'];?>;
      messageRaw.publickey = pubKey.slice(2);

      //construct message to be signed
      var message = messageRaw.amount.toString() + messageRaw.recipient +
                    messageRaw.itemID.toString() + messageRaw.publickey;

      var signature = KJUR.crypto.ECDSA.asn1SigToConcatSig(sign(message));
      $.post('formProcess.php',{
        amount: messageRaw.amount,
        publickey: messageRaw.publickey,
        recipient: messageRaw.recipient,
        itemID: messageRaw.itemID,
        signature: signature
      })
      .done(function(data){
        console.log(data);
        window.location = 'account.php';
      });

    }else{
        alert('invalidKeys');
        $('#privateKey').val('');
    }

  });




  function verifyKeyPair() {
    var sigValueHex = sign("aaa");

    var sig = new KJUR.crypto.Signature({"alg": hashFunc, "prov": "cryptojs/jsrsa"});
    sig.init({xy: pubKey, curve: curve});
    sig.updateString("aaa");

    return sig.verify(sigValueHex);
    return true;
  }


  function sign(message) {
    messageTwo = 'a';
    var sig = new KJUR.crypto.Signature({"alg": hashFunc});

    sig.init({d: privKey, curve: curve});
    sig.updateString(message);

    return sig.sign();
  }



</script>
</body>
</html>
<!--https://bootsnipp.com/snippets/featured/address-details-modal-form-->
