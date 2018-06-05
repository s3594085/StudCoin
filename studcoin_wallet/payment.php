<?php session_start();
var_dump($_SESSION);
?>
<!doctype html>
<!--PrivateKey: 654258b7a4e5b29fe49dfa2f95b109a427a222868f44df3a0f866ed6d42f313d-->
<!--PublicKey: fb066f5451334bddaf1a216a9feb1918f103467f7ae2ff6eb8a4b138d7315387feca243e705ca600ce49a4dd3a88ba4f28d0b45e0f2de44cb2c396599bd23524-->
<html lang="en">
<script language='JavaScript' type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.4/jsrsasign-all-min.js'></script>
    </head>
<?php include('include/head.inc.php');?>
<link rel="stylesheet" type="text/css" href="css/payment.css">
<?php include('include/nav.inc.php');?>
<body>
    <div class ="row ">
        <div class ="col-sm-12 col-md-12 ">
              <div id="item">
                  <?php
                    include('storeData.php');
                    getItem($_GET['requestedItemID']);
                    $_SESSION['cart']['itemID'] = $_GET['requestedItemID'];
                    $_SESSION['cart']['seller'] = $_GET['seller'];
                    $_SESSION['cart']['amt'] = $_GET['amt'];
                  ?>
              </div>
        </div>
        <hr>
        <div class="container col-sm-6 col-md-6">
              <div id='keysForm'>
                  <br><br><br>
                  <p>Public Key</p>
                  <input type="text" id="publicKey"style="width:450px;" value="<?php echo $_SESSION['publicKey'];?>"readonly>
                  <p>Enter Your Private Key</p>
                  <input type="text" id="privateKey"style="width:450px;">
                  <!-- <input type="file"> -->
              </div>
              <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <div class="pull-right">
                          <button type="submit" id="checkout" class="btn btn-success"">Buy</button>
                          <button type="submit" id="cancel" class="btn btn-default">Cancel</button>
                    </div>
                  </div>
              </div>
        </div>
    </div>



<script>

  var pubKey = $.trim($('#publicKey').val());
  var privKey;
  var curve = "NIST P-256";
  var hashFunc = "SHA256withECDSA";

  $("#cancel").click(function(){
      window.location = 'index.php';
  });
  $("#checkout").click(function(){
    privKey = $.trim($('#privateKey').val());
    //validate keypair first
    console.log(verifyKeyPair())
    if(verifyKeyPair()){
      var messageRaw = new Object();
      messageRaw.amount = <?php echo $_SESSION['cart']['amt'];?>;
      messageRaw.recipient = '<?php echo $_SESSION['cart']['seller'];?>';
      messageRaw.itemID = <?php echo $_SESSION['cart']['itemID'];?>;
      messageRaw.publickey = pubKey;
      var message = JSON.stringify(messageRaw);
      var signature = sign(message);
      $.post('formProcess.php',{
        amount: messageRaw.amount,
        publickey: messageRaw.publickey,
        recipient: messageRaw.recipient,
        itemID: messageRaw.itemID,
        signature: signature
      })
      .done(function(data){
        console.log(data);
      });
     window.location = 'account.php';
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
  }


  function sign(message) {
    var sig = new KJUR.crypto.Signature({"alg": hashFunc});

    sig.init({d: privKey, curve: curve});
    sig.updateString(message);

    return sig.sign();
  }







</script>
</body>
</html>
<!--https://bootsnipp.com/snippets/featured/address-details-modal-form-->
