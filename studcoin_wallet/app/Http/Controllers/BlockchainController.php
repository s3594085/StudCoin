<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;
use App\Item;
use App\Transaction;

class BlockchainController extends Controller
{
    public function sendTransaction(Request $request){
      $postdata =
            array(
              'nodes' => [],
              'signature' => $request->input('signature'),
              'message' => array(
                        'amount' => (int)$request->input('amount'),
                        'recipient' => $request->input('recipient'),
                        'itemID' => (int)$request->input('itemID'),
                        'publickey' => $request->input('publickey')
                      )
            );

        $result = $this->makeRequest($postdata, 'transactions/new');
        return $result;
        //
        // if($this->page_title($result)!="200 Internal Server Error"){
        //   return "Block is being mined. Please around a minute";
        // }
        //
        // return $result;

    }

    public function sendToBlockChain(Request $request){
      $postdata =
      			array(
      				'nodes' => [],
      				'signature' => $request->input('signature'),
      				'message' => array(
      									'amount' => (int)$request->input('amount'),
      									'recipient' => $request->input('recipient'),
      									'itemID' => (int)$request->input('itemID'),
      									'publickey' => $request->input('publickey')
      								)
      			);

        $result = $this->makeRequest($postdata, 'transactions/new');
  	    if($result===FALSE){
            var_dump($http_response_header);
  	    }
        else{
          //update the database
          $transactionId = json_decode($result)->txid;
          // dd($transactionId);
          $item = Item::find($request->input('itemID'));
          $item->status = 'sold';
          $item->save();

          $transaction = new Transaction;
          $transaction->seller = $item->seller;
          $transaction->buyer = Auth::user()->publicKey;
          $transaction->itemId = $item->id;
          $transaction->transaction_id = $transactionId;
          $transaction->save();

        }
    }


    public function getTransactionDetails(Request $request){

      $transactionId = Transaction::select('transaction_id')->where('itemId', $request->itemId)->first();
      $postdata =
          array(
            'id'=> $transactionId->transaction_id
          );

      $result = $this->makeRequest($postdata, 'findTransaction');
      if($this->page_title($result)=="500 Internal Server Error"){
        return "Block is being mined. Please around a minute";
      }
      return $result;

    }


    public function purchaseCoins(Request $request){

        $requestedAmount = (int)$request->input('amount');
        $postdata =
            array(
              'amount'=> (int)$requestedAmount,
              'publickey' => Auth::user()->publicKey,
              'recipient' => Auth::user()->publicKey
            );

        $result = $this->makeRequest($postdata, 'buyCoins');

        $res = (strcmp($result,"Node does not have enough funds, try another node"));
        if($res==0){
          return 'invalid';
        }else{
          return 'valid';
        }
    }

    public static function getUTXO($publicKey){
        $postdata =
         array(
           'publickey' => $publicKey
         );



       $result = (new BlockchainController)->makeRequest($postdata, 'getBalance');
       $balance = json_decode($result, true);
       return $balance['balance'];
    }



    private function makeRequest($options, $urlMethod){


      $server = '127.0.0.1';
      $port = '5010';
      $url = 'http://'.$server.':'.$port.'/'.$urlMethod;
      // $context = stream_context_create($options);
      // $result = file_get_contents($url, false, $context);
      // return $result;



      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options));
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen(json_encode($options)))
      );


      $data = curl_exec($ch);
      curl_close($ch);

      return $data;
    }

    private function page_title($page) {
      if (!$page) return null;

      $matches = array();

      if (preg_match('/<title>(.*?)<\/title>/', $page, $matches)) {
          return $matches[1];
      } else {
          return null;
      }
}






}
