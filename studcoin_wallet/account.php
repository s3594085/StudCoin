<?php session_start();?>
<!doctype html>

<html lang="en">

<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<body>
    <h1 style="text-align:center">Your Transactions</h1>
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
