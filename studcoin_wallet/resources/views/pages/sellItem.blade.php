@extends('layouts.layout')
@section('content')
<div class="wrapper fadeInDown">
    <div id="formContent">
        <!-- Tabs Titles -->

        <!-- Icon -->
        <div class="fadeIn first">
            </br></br>
            <h1>Sell Item</h1>
            </br></br>
        </div>
        <div class="addItems">
          <form class="form-horizontal" method="POST" action="{{ route('addItemToDatabase') }}" enctype="multipart/form-data">
          {{ csrf_field() }}
          <input type="text" id="itemName" class="fadeIn second" name="name" placeholder="Item Name">
          <input type="text" id="itemDescription" class="fadeIn second" name="description" placeholder="Desciption and condition of item">
          <br>$<input type="number" id="price" name="price" width="25%" min="0" max="9999" step="0.01"placeholder="Price of item"/>
          <br><br>
          <span>Please upload image</span>
          <br>
          <input type="file" name="img" id="fileinput" style="margin:0 auto; width:46%">
          </br></br>
          <input id="sell" type="submit" class="fadeIn second" value="Sell" >
        </form>
        </div>
    </div>
</div>
<div id="editor"></div>
@endsection
