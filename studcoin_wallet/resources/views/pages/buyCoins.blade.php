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
                  <div class="BuyCoins" style="padding-left:50px;padding-right:50px;">
                      {{ csrf_field() }}
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

<script>
var token = $('input[name=_token]').val();

$("#requestCoinsSubmit").click(function(){
    var enteredAmount = $("#amount").val();
    var positiveNumberRegex = new RegExp("^[1-9]\\d*$");
    if(!positiveNumberRegex.test(enteredAmount)){
      alert('please enter a positive number');
      $('#amount').val('');
      return;
    }
    else{
      //$('#buyCoin').submit();
      $.post('{{Route('purchaseCoins')}}', {amount: enteredAmount, _token:token})
        .done(function(data){
              if(data=='invalid'){
                  $("#errorNotify").html('Requested amount is unavailable. Please choose another amount');
              }else{
                  alert('balance updated');
                  window.location.replace('{{Route('index')}}');
              }
        })
        .fail(function(data){
            $("#errorNotify").html('Server Error');

        })
        ;
    }
});

$("#amount").click(function(){
    $("#errorNotify").html("");
    $("#amount").val("");
});
</script>

@endsection
