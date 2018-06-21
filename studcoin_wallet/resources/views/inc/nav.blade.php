<nav class="navbar navbar-expand-sm bg-info navbar-dark">
  <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="/index">Home</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="/buyCoins">Buy Studcoins</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="/sendCoins">Send Studcoins</a>
      </li>
      <li class="nav-item active">
      	<span class="nav-link">Balance :
          @if(Auth::check())
          {{App\Http\Controllers\BlockchainController::getUTXO(Auth::user()->publicKey)}}</span>
          @endif
      </li>

  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item active">
      <a class="nav-link" href="index">Buy Items</a>
    </li>
    <li class="nav-item active">
      <a class="nav-link" href="/sellItem">Sell Item</a>
    </li>
    <li class="nav-item dropdown active">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Account
      </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="/account">Transactions</a>
          <a class="dropdown-item" href="{{route('logout')}}"
              onclick="event.preventDefault();
              document.getElementById('logout-form').submit();">Logout
          </a>
          <form id="logout-form" action="{{route('logout')}}" method="POST" style="display: none;">
              {{ csrf_field() }}
          </form>
        </div>
    </li>

  </ul>
</nav>
