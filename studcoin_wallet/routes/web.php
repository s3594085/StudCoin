<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('/index', 'itemController@fetchUser')->name('index');

Route::get('/sellItem', function(){
  return view('pages.sellItem');
});

Route::get('/sendCoins', function(){
  return view('pages.sendCoins') ;
});

Auth::routes();

Route::get('/buyCoins',function () {
  return view('pages.buyCoins');
});

Route::get('/account', 'itemController@getUserTransactions');

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/payment/{itemId}', ['uses'=>'itemController@getItem']);

Route::post('/purchaseCoins', 'BlockchainController@purchaseCoins')->name('purchaseCoins');

Route::post('/formRequest', 'BlockchainController@sendToBlockChain');

Route::post('/addItemToDatabase', 'itemController@store')->name('addItemToDatabase');

Route::post('/getTransactionDetails', 'BlockchainController@getTransactionDetails');

Route::post('/sendTransaction', 'BlockchainController@sendTransaction');
