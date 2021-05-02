<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item {{ Request::routeIs('home') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('home') }}">
        <i class="mdi mdi-home menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item {{ Request::routeIs('transaction.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('transaction.search') }}">
        <i class="mdi mdi-cached menu-icon"></i>
        <span class="menu-title">Check Transactions</span>
      </a>
    </li>

    <li class="nav-item {{ Request::routeIs('transactions.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('transactions.index') }}">
        <i class="mdi mdi-cached menu-icon"></i>
        <span class="menu-title">Transactions</span>
      </a>
    </li>

    <li class="nav-item {{ Request::routeIs('topup.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('topup.index') }}">
        <i class="mdi mdi-cached menu-icon"></i>
        <span class="menu-title">Topup</span>
      </a>
    </li>
    
    
      <li class="nav-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('users.index') }}">
          <i class="mdi mdi-account-multiple menu-icon"></i>
          <span class="menu-title">Users</span>
        </a>
      </li>
    
    
  </ul>
</nav>