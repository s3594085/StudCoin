<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Item;
use App\Transaction;


class itemController extends Controller
{
    public function fetchUser(){
          $items = DB::select("SELECT * from items
            WHERE status='available'


            ");
          return view('pages.main')->with('items',$items);
    }

    public function getItem($itemId){
      return view('pages.payment')->with('item',Item::find($itemId));
    }

    public function getUserTransactions(){
      $user = Auth::user()->publicKey;
      //Select all items the user has purchased
      $purchasequery = "SELECT items.* FROM items
                            LEFT JOIN soldItems
                            ON items.id=soldItems.itemId
                            WHERE soldItems.buyer='$user';
                             ";
      $purchasedItems = DB::select($purchasequery);


      $soldQuery = "SELECT * FROM items
                    WHERE seller='$user'";

      $soldItems =  DB::select($soldQuery);

      return view('pages.account')->with(compact('purchasedItems', 'soldItems'));
    }

    public function store(Request $request){
      $newItem = new Item;

      $newItem->name = $request->name;


      $latestId = Item::orderBy('id', 'desc')->first();
      if(empty($latestId)){
        $latestId = 0;
      }else{
        $latestId = $latestId->id + 1; 
      }

      Storage::putFileAs('public/img/', $request->file('img') , "$latestId." . $request->img->getClientOriginalExtension());


      $newItem->image = "img/$latestId." . $request->img->getClientOriginalExtension();
      $newItem->price = $request->price;
      $newItem->seller = Auth::user()->publicKey;
      $newItem->status = 'available';
      $newItem->description = $request->description;
      $newItem->save();
      return redirect(route('index'));
    }
}
