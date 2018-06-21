<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
  protected $table = 'soldItems';


  public function item($itemId){
    return $this->hasOne('App\Item', 'id');
  }
}
