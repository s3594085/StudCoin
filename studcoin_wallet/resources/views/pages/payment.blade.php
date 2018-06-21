@extends('layouts.layout')

@section('content')
  <div class="container" style="width:50%;">
      <div id="item">
         <div class="container row">
                 {{ csrf_field() }}
               <span class ="container col-sm-4">Image</span>
               <span class ="container col-sm-4">Name</span>
               <span class ="container col-sm-4">Price</span>

               <div class ="container col-sm-4">
                   <div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">
                       <img src={{asset('storage/' . $item->image)}} alt="IMG-PRODUCT"class="img-fluid">
                   </div>
               </div>
               <div class ="container col-sm-4 my-auto">
                     <span>{{$item->name}}</span>
               </div>
               <div class ="container col-sm-4 my-auto">
                     <span>{{$item->price}}</span>
               </div>
          </div>
       </div>
       <hr>
       <div id='keysForm' class="container" style="margin-left:12%;">
           <br><br><br>
           <p>Public Key</p>
           <input type="text" id="publicKey"style="width:450px;" value="{{Auth::user()->publicKey}}"readonly>
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
    var pubKey = '04'+'{{Auth::user()->publicKey}}';
    var privKey;
    var curve = "NIST P-256";
    var hashFunc = "SHA256withECDSA";
    var token = $('input[name=_token]').val();


    $("#cancel").click(function(){
        window.location = 'index.php';
    });
    $("#checkout").click(function(){
      privKey = $.trim($('#privateKey').val());
      //validate keypair first
      if(verifyKeyPair()){
        var messageRaw = new Object();
        messageRaw.amount = '{{$item->price}}';
        messageRaw.recipient = '{{$item->seller}}';
        messageRaw.itemID = '{{$item->id}}';
        messageRaw.publickey ='{{Auth::user()->publicKey}}';

        //construct message to be signed
        var message = messageRaw.amount.toString() + messageRaw.recipient +
                      messageRaw.itemID.toString() + messageRaw.publickey;

        var signature = KJUR.crypto.ECDSA.asn1SigToConcatSig(sign(message));
        $.post('/formRequest',{
          amount: messageRaw.amount,
          publickey: messageRaw.publickey,
          recipient: messageRaw.recipient,
          itemID: messageRaw.itemID,
          signature: signature,
          _token: token
        })
        .done(function(data){
          window.location = '/account';
        }).fail(function(data){
          alert('purchase failed');
          console.log(data);
        })
        ;

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



@endsection
