<?php session_start();?>
<!doctype html>
<!--PrivateKey: 654258b7a4e5b29fe49dfa2f95b109a427a222868f44df3a0f866ed6d42f313d-->
<!--PublicKey: fb066f5451334bddaf1a216a9feb1918f103467f7ae2ff6eb8a4b138d7315387feca243e705ca600ce49a4dd3a88ba4f28d0b45e0f2de44cb2c396599bd23524-->
<html lang="en">

<?php include('include/head.inc.php');?>
<?php include('include/nav.inc.php');?>
<body>
    <h1><?php echo 'username :'.$_SESSION['username'] ;
    echo '<br>email : '.$_SESSION['email']; 
    echo '<br>privateKey : '.$_SESSION['privateKey'];
    echo '<br>publicKey : '.$_SESSION['publicKey'];
    ?></h1>
        
        
   
<script>

</script>    
</body>
</html>
<!--https://bootsnipp.com/snippets/featured/address-details-modal-form-->