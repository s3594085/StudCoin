@extends('layouts.layout')
@section('content')
<h1> Purchased Items</h1>
<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
   <div class="modal-content">
     <div class="modal-header">
       <h4>Transaction in blockchain</h4>
       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
       </button>
     </div>
     <p>
     <div class="modal-body" style="overflow:auto;">

     </div>
     <p>

   </div>
 </div>
</div>
<div class="row">
    @foreach($purchasedItems as $item)
          <div class="col-sm-6 col-md-4 col-lg-3 p-b-50">
          {{ csrf_field() }}
          <div class="block container" id="item1">
          <div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">
          <img src="{{asset('storage/' . $item->image)}}" alt="IMG-PRODUCT" width=720 class="img-fluid">
           </div>
          <div class="row">
          <div class="col-sm">
          <p>{{$item->name}}</p>
          <p>{{$item->price}}</p>
          </div>
          <div class="col-sm">
          @if($item->status=='pending')
              <button type="button" class="float-right btn btn-lg btn-success" disabled>Purchased</button>
          @elseif($item->status=='purchased')
              <button type="button" class="float-right btn btn-lg btn-success" disabled>Yours</button>
          @endif
          <button type="button" id="details" class="float-right btn btn-lg btn-success" onclick="getItemDetails({{$item->id}})">Details</button>
          </div>
          </div>
          </div>
          </div>
  @endforEach
</div>
<h1> Sold Items</h1>
<div class="row">
    @foreach($soldItems as $item)
          <div class="col-sm-6 col-md-4 col-lg-3 p-b-50">
          <form action="payment.php" method="get">
          <div class="block container" id="item1">
          <div class="block-img wrap-pic-w of-hidden pos-relative block-labelnew">
          <img src="{{asset('storage/' . $item->image)}}" alt="IMG-PRODUCT" width=720 class="img-fluid">
           </div>
          <div class="row">
          <div class="col-sm">
          <p>{{$item->name}}</p>
          <p>{{$item->price}}</p>
          </div>
          <div class="col-sm">
          @if($item->status=='sold')
              <button type="button" id="details" class="float-right btn btn-lg btn-success" onclick="getItemDetails({{$item->id}})">Sold</button>
          @elseif($item->status=='available')
              <button type="button" class="float-right btn btn-lg btn-warning" disabled>In Store</button>
          @endif
          </div>
          </div>
          </div>
          </form>
          </div>
  @endforEach
</div>
<script>

function getItemDetails($itemId){
    var token = $('input[name=_token]').val();
    $.post('/getTransactionDetails',{
        itemId : $itemId,
        _token: token
    }
  ).done(function(data){
    if(data=='The server encountered an internal error and was unable to complete your request. Either the server is overloaded or there is an error in the application.'){
        $(".modal-body").html('Your transaction is being mined. Please wait for one minute');
    }else{
        $(".modal-body").html(data);
    }
    $("#exampleModalCenter").modal();
    console.log(data);
  });
}

</script>
@endsection
