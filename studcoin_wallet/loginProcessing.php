<?php 
	//public key ::0498a4cd7d92583558ce2f2c67a3f8252dcd9ad6057ed44cfbd9b838aec0c0f958c91d0a3855aa48081fa27f3bff83dc1edae13967d5d0cd7410f351d0ace1d56f
	//private key :: 3d4b597814e7ac821cf87efa496b7b5daca719b0f1d9e6b10ef1d1b52ef59d56 
	session_start(); 
	$username = $_POST['username']; 
	$password = $_POST['password']; 
	$pwdmd5 = md5($password);
	if(isset($_POST['login'])){
		$file_db = new PDO('sqlite:db_test.db');
		$file_db->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION); 
		$qry = "SELECT username, publicKey
			FROM account
			WHERE username='$username' AND password='$pwdmd5'";  
		$result = $file_db->query($qry);
		$row = $result->fetch();
		if(!$result || $row<=0 ){
			echo "The username and password do not match";
		}else{
			$publicKey = $row['publicKey']; 
			$username =	$row['username']; 	
			$_SESSION['publicKey'] = $publicKey;
			$_SESSION['username'] = $username; 
			$_SESSION['utxo'] = getUTXO('000116e05a02f0f2b553c041e060ac036b8ebaa1dde1da711b9f6db6c70a6db1b6f50e940246e7e28f908477da6ec982cad2c744610550b65617a19d8fa328b9');
		}
	}elseif(isset($_POST['newUser'])){
		$username = $_POST['username']; 
		$password = md5($_POST['password']); 
		$email = $_POST['email']; 
		$publicKey = $_POST['pubKey']; 

		$file_db = new PDO('sqlite:db_test.db');
		$file_db->setAttribute(PDO::ATTR_ERRMODE,
		PDO::ERRMODE_EXCEPTION); 
		$qry = "INSERT INTO account(email, username, password,publicKey)
		VALUES ('$email', '$username', '$password', '$publicKey')";  
		$file_db->exec($qry);
	}
	
	//------------------------------------HC------------------------------------
	//fetch UTXO
	function getUTXO($publicKey){
	   		$postdata = 
			array(
				'publickey' => $publicKey
			);		


			$opts=array('http' =>
				 array(
			        'method'  => 'POST',
			        'header'  => 'Content-type: application/json',
			        'content' => json_encode($postdata)
		   		 )
			);
			$context  = stream_context_create($opts);
			$server = '127.0.0.1';
			$port = '5000';
			$url = 'http://'.$server.':'.$port.'/getBalance';
			
			$result = file_get_contents($url, false, $context); 
			$balance = json_decode($result, true);
			return $balance['balance'];
	}

	







	

?> 