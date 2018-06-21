@extends('layouts.layout')
@section('content')
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
              {{ csrf_field() }}
              <span style="float:left;font-size:20px">Balance: </span>
              <span id="balance" style="float:right;font-size:20px">0</span>
              </br>
              <hr>
              <h5>Send Transaction</h5>
              <input type="text" id="recipient" class="fadeIn second" name="recipient" placeholder="Recipient Public Address">
              <input type="text" id="amount" class="fadeIn second" name="public" placeholder="Amount to send">
              <input type="text" id="privKey" class="fadeIn second" name="public" placeholder="Your Private Key">
              </br></br>
              <input id="send" type="submit" class="fadeIn second" value="Send">
          </div>
    </div>
</div>
<script>
var curve = "NIST P-256";
var hashFunc = "SHA256withECDSA";
var privKey;
$("#send").click(function(){
  privKey = $("#privKey").val();
  var token = $('input[name=_token]').val();
  var amount = $("#amount").val();
  var recipient = $("#recipient").val();
  var pubkey = '{{Auth::user()->publicKey}}';
  var message = amount.toString() + recipient +
                '0' + pubkey;

  var signature = KJUR.crypto.ECDSA.asn1SigToConcatSig(sign(message));
  //console.log(signature);

  $.post('/sendTransaction',{
    amount: amount,
    publickey: pubkey,
    recipient: recipient,
    itemID: 0,
    signature: signature,
    _token: token
  })
  .done(function(data){

    if(data=='Invalid Signature'){
      alert('Invalid private key');
    }else if(data=='Insufficient Balance'){
      alert('Insufficient balance');
    }else if(data=='Amount cannot be negative'){
      alert('amount cannot be negative');
    }
    else{
      alert('Transaction Sent');
      // window.location = '/account';
    }
    console.log(data);
  }).fail(function(data){
    alert('purchase failed');
    console.log(data);
  })
  ;

})

function sign(message) {
  messageTwo = 'a';
  var sig = new KJUR.crypto.Signature({"alg": hashFunc});

  sig.init({d: privKey, curve: curve});
  sig.updateString(message);

  return sig.sign();
}



</script>

@endSection
