<?php session_start();?>
<!doctype html>
<!--PrivateKey: 654258b7a4e5b29fe49dfa2f95b109a427a222868f44df3a0f866ed6d42f313d-->
<!--PublicKey: fb066f5451334bddaf1a216a9feb1918f103467f7ae2ff6eb8a4b138d7315387feca243e705ca600ce49a4dd3a88ba4f28d0b45e0f2de44cb2c396599bd23524-->
<html lang="en">

<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<body>
    <h1>
    <?php 
    echo 'Balance : '. getUTXO('000116e05a02f0f2b553c041e060ac036b8ebaa1dde1da711b9f6db6c70a6db1b6f50e940246e7e28f908477da6ec982cad2c744610550b65617a19d8fa328b9') . ' Studcoins<br>'; 
    
   		function getUTXO($publicKey){
	   		$server = '127.0.0.1';
			$port = '5000';
			$url = 'http://'.$server.':'.$port.'/getUTXO';
			$result = file_get_contents($url);
			$resultJSON = json_decode($result, true);
			
			foreach($resultJSON as $a){
			foreach($a as $b){
				foreach($b as $utxo){
					if($utxo['address']==$publicKey){
						return $utxo['amount'];
				}
			}}
			}
		}
		
	?>
	Your Transactions
    </h1>
    <div id="userItems">


    </div>   
        
   
<script>
	var publicKey = '<?php echo $_SESSION['publicKey'];?>';
	window.onload = function(){
		$.post('storeData.php', {getAllUserPurchases: 'true', userID: publicKey})
			.done(function(data){
				$('#userItems').html(data); 
			});



	}
</script>    
</body>
</html>
<!--https://bootsnipp.com/snippets/featured/address-details-modal-form-->