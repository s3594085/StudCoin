<?php 



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