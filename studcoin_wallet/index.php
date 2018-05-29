<!DOCTYPE html>
<html>
    <head>
        <title>StudCoin Wallet</title>

        <link href="css/custom.css" rel="stylesheet">
        <link href="css/bootstrap.min.css" rel="stylesheet">
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        
        <script language='JavaScript' type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/8.0.4/jsrsasign-all-min.js'></script>
    </head>
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

                <!-- Detail Form -->
                <div class="verify">
                    <div class="existing">
                        <input type="text" id="public" class="fadeIn second" name="public" placeholder="Enter your public key">
                        <input type="text" id="private" class="fadeIn second" name="private" placeholder="Enter your private key">
                        </br></br>
                        <input id="verify" type="submit" class="fadeIn second" value="Get Verify">
                    </div>
                    
                    <div class="new" style="display:None">
                        <input id="keypair" type="submit" class="fadeIn first" value="Generate">
                    </div>
                    
                    <div class="keypair" style="display:None">
                        <h5>Public Key</h5>
                        <input type="text" id="publicK" class="fadeIn first" readonly>
                        <h5>Private Key</h5>
                        <input type="text" id="privateK" class="fadeIn first" readonly>
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
                    <button id="new" class="underlineHover nbtn">New Wallet!</button>
                </div>
                
                <div id="formFooter" class="new" style="display:None">
                    <button id="existing" class="underlineHover nbtn">Existing Wallet!</button>
                </div>
            </div>
        </div>
    </body>

<script>
/* Global Declaration */
var curve = "NIST P-256";
var hashFunc = "SHA256withECDSA";
var pubKey = null;
var privKey = null;

/* Actions */
$("#new").click(function(){
    $('.new').show();
    $('.existing').hide();
    
    $('.verify').show();
    $('.verified').hide();
    
    $('.sent').hide();
    
    clear();
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

$("#verify").click(function(){
    pubKey = $("#public").val();
    privKey = $("#private").val();
    
    if (verifyKeyPair()) {
        $('.verify').hide();
        $('.verified').show();
        
        var server = "127.0.0.1";
        var port = "5000";
        
        //window.location.href
        var url = window.location.href + "/api.php?server=" + server + "&port=" + port + "&pubKey=" + pubKey;
        var data = JSON.parse($.ajax({type: "GET", url: url, async: false}).responseText);
                
        $("#balance").text(data.balance);
    } else {
        alert("Public Key & Private Key are not compatible!");
    }
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
</script>

</html>