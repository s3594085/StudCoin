@extends('layouts.layout')

@section('content')
<h1 style="text-align:center;">StudCoin Store</h1>
<div class="container">
  <div class="itemsList row">
    @foreach($items as $i => $item)
            <div class="canClick col-sm-6 col-md-4 col-lg-3 p-b-50">

                <div class="block container" id="item1">
                  <div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">
                      <img src={{asset('storage/' . $item->image)}} alt="IMG-PRODUCT" width="720" height="960" class="img-fluid">
                   </div>
                  <div class="itemDetails">
                      <p>{{$item->name}}</p>
                      <div id ="pricePurchase">
                          <span class="container" id="price">{{$item->price}}</span>
                          @if($item->price>App\Http\Controllers\BlockchainController::getUTXO(Auth::user()->publicKey))
                          <button class="container btn btn-lg bg-danger" type="submit" name="itemName" id="buyButton" disabled>Insufficient<br>funds</button>
                          @else
                          <button class="container vertical-center btn btn-lg bg-success" onclick="window.location.href='payment/{{$item->id}}'" type="submit" name="itemName" id="buyButton)">Buy<br>&nbsp</button>
                          @endif
                      </div>
                  </div>
                </div>

            </div>
    @endforeach
  </div>
</div>
@endsection
