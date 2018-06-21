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

                  <!-- Detail Form -->
                  <div class="login">
                    <form class="form-horizontal" method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}
                      <div class="existing">
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong class="danger">{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                        <br>
                          <input type="text" id="username" class="fadeIn second" name="email" placeholder="Enter your email" value="{{ old('email') }}">

                          <input type="password" id="password" class="fadeIn second" name="password" placeholder="Enter your password">

                          </br></br>
                          <input id="login" type="submit" class="fadeIn second" value="Login">
                      </div>
                    </form>

                      <div class="new" style="display:None">
                          <form class="form-horizontal" id="register_form" method="POST" action="{{ route('register') }}">
                            {{ csrf_field() }}
                            <!--<input type="text" id="newUsername" class="fadeIn second" name="email" placeholder="Enter your username">-->
                            <input type="text" id="email" class="fadeIn second" name="email" placeholder="Enter your email">
                            <input type="password" id="newPassword" class="fadeIn second" name="password" placeholder="Enter your password">
                            <input type="password" id="confirmPassword" class="fadeIn second" name="password_confirmation" placeholder="Confirm your password">
                            <input type="hidden" name="publicKey" id="hPublicK" class="fadeIn first">
                          </form>
                          <input id="register" type="submit" class="fadeIn second" value="Register">
                      </div>

                      <div class="keypair" style="display:None">
                          <h5>Public Key</h5>
                          <input type="text" id="publicK" class="fadeIn first" readonly>
                          <br><br><h5>Private Key</h5>
                          <h5>Copy your private key. You need it later on!</h5>
                          <p><input type="text" id="privateK" class="fadeIn first" readonly>
                          <input type="button" class="fadeIn second" id="copyClipboard" value="Copy Private Key"></p>
                          <input id="returnLogin" form="register_form" type="submit" class="fadeIn second" value="Login" disabled>
                          <br><br>
                      </div>
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

                  <div class="sent" style="display:None">
                      <div class="check_mark">
                          <div class="sa-icon sa-success animate">
                              <span class="sa-line sa-tip animateSuccessTip"></span>
                              <span class="sa-line sa-long animateSuccessLong"></span>
                              <div class="sa-placeholder"></div>
                              <div class="sa-fix"></div>
                          </div>
                      </div>
                      <input id="back" type="submit" class="fadeIn second" value="Back">
                  </div>

                  <!-- Footer -->
                  <div id="formFooter" class="existing">
                      <button id="new" class="underlineHover nbtn">New User</button>
                  </div>

                  <div id="formFooter" class="new" style="display:None">
                      <button id="existing" class="underlineHover nbtn">Existing Wallet!</button>
                  </div>
              </div>
          </div>

  <script>
  /* Global Declaration */
  var curve = "NIST P-256";
  var hashFunc = "SHA256withECDSA";
  var pubKey = null;
  var privKey = null;

  /* Actions */

  $("#copyClipboard").click(function(){
      $("#privateK").select();
      document.execCommand("copy");
      $("#returnLogin").prop('disabled', false);
  })

  $("#returnLogin").click(function(){

      // window.location.replace('index.php');
      //$("register_form").submit();

  });

  $("#new").click(function(){
      $('.new').show();
      $('.existing').hide();

      $('.verify').show();
      $('.verified').hide();

      $('.sent').hide();

      clear();
  });

  $('#register').click(function(){
      //validate the form
      var username = $('#newUsername').val();
      var email  = $('#email').val();
      var password = $('#newPassword').val();
      var confirmPassword = $('#confirmPassword').val();



      if(username==""||email==""||password==""||confirmPassword==""){
          alert('One of the fields is empty');
          return;
      }
      var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      if(!re.test(String(email).toLowerCase())){
          alert("please enter in email format");
          return; 
      }

      if(password!=confirmPassword){
          alert('Passwords entered do not match');
          return;
      }

      $('.new').hide();
      $('.keypair').show();
      var kp = generateKeyPair();

      // Generating Public Key
      var publicK = kp.ecpubhex;

      // Generating Private Key
      var privateK = kp.ecprvhex;

      $("#publicK").val(publicK.substring(2));
      $("#privateK").val(privateK);
      $("#hPublicK").val(publicK.substring(2));

  });

  $("#existing").click(function(){
      $('.existing').show();
      $('.new').hide();
      $('.keypair').hide();

      $('.verify').show();
      $('.verified').hide();
  });

  $("#keypair").click(function(){
      $('.keypair').show();

      var kp = generateKeyPair();

      // Generating Public Key
      var publicK = kp.ecpubhex;

      // Generating Private Key
      var privateK = kp.ecprvhex;

      $("#publicK").val(publicK);
      $("#privateK").val(privateK);
  });


  $("#shopping").click(function(){
      window.location.replace('index.php');


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

  $("#back").click(function(){
      $('.sent').hide();
      $('.verified').show();

      $("#recipient").val("");
      $("#amount").val("");
  });

  /* JavaScript Functions */
  function generateKeyPair() {

      var ec = new KJUR.crypto.ECDSA({"curve": curve});

      var keypair = ec.generateKeyPairHex();

      return keypair;
  }

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

  function clear() {
      pubKey = null;
      privKey = null;

      $("#public").val("");
      $("#private").val("");
      $("#recipient").val("");
      $("#amount").val("");
  }

  function validateEmail(email) {

  }
  </script>

  </html>

@endsection
