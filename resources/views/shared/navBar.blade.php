<!-- ===== NAVBAR ===== -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex" href="{{ route('home') }}">
            <span class="brand-icon me-2">♻</span>
            SwapSustain
        </a>

        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                @if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin')
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">
                                </i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">
                            Collections</a></li>
                    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">
                            Dashboard</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" data-bs-toggle="dropdown"
                            role="button">
                            
                            {{ Auth::guard('user')->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('register') }}">
                                    <i class="fas fa-user-plus me-1"></i>Add Admin</a></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout</a></li>
                        </ul>
                    </li>
                @elseif (Auth::guard('user')->check())
                    <li class="nav-item"><a class="nav-link active" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('request') }}">My Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('swap') }}">My Swaps</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">Collections</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{ Auth::guard('user')->user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            @if (Auth::guard('user')->user()->role == 'seller')
                                <li><a class="dropdown-item logout" href="{{ route('add_item') }}">Add Item</a></li>
                            @endif
                            <li><a class="dropdown-item logout" href="{{ route('logout') }}">Logout</a></li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item"><a class="nav-link active" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('request') }}">My Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('swap') }}">My Swaps</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">Collections</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>
