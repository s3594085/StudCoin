<nav class="navbar navbar-expand-sm bg-info navbar-dark">
  <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.php">Home</a>
      </li>
      <li class="nav-item active">
        <a class="nav-link" href="buyCoins.php">Buy Studcoins</a>
      </li>
      <li class="nav-item active">
      	<span class="nav-link">Balance : <?php echo($_SESSION['utxo']);?></span>
      </li>

  </ul>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item active">
      <a class="nav-link" href="index.php">Buy Items</a>
    </li>
    <li class="nav-item active">
      <a class="nav-link" href="addItem.php">Sell Item</a>
    </li>
    <li class="nav-item dropdown active">
      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Account
      </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
          <a class="dropdown-item" href="account.php">Transactions</a>
          <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
    </li>

  </ul>
</nav>
