<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Collection Clothes</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}">
    <link href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('assets/Collections/css/collections.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/shared/css/style.css') }}">
</head>

<body>
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
                <ul class="navbar-nav ms-auto ">
                    @if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin')
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">
                                <i class="fas fa-home me-1"></i>Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">
                                <i class="fas fa-tshirt me-1"></i>Collections</a></li>
                        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-shield-alt me-1"></i>Dashboard</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-user-shield me-1"></i>
                                {{ Auth::guard('user')->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item text-black alert-success"
                                        href="{{ route('register') }}">Add
                                        Admin</a></li>
                                <li><a class="dropdown-item logout" href="{{ route('logout') }}">Logout</a></li>
                            </ul>
                        </li>
                    @elseif (Auth::guard('user')->check())
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href='{{ route('request') }}'>My Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('swap') }}">My Swaps</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">Collections</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                {{ Auth::guard('user')->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @if (Auth::guard('user')->user()->role == 'seller')
                                    <li><a class="dropdown-item text-black" href="{{ route('add_item') }}">Add Item</a>
                                    </li>
                                @endif
                                <li><a class="dropdown-item logout" href="{{ route('logout') }}">Logout</a></li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href='{{ route('request') }}'>My Requests</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('swap') }}">My Swaps</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('collections') }}">Collections</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endif
                    <div class="currency-switch" onclick="toggleCurrency()">
                        <div class="track">
                            <div class="thumb"></div>
                        </div>
                        <div class="labels">
                            <span>🇪🇬</span>
                            <span>🇺🇸</span>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Title -->
    <div class="container my-5 d-flex align-items-center justify-content-evenly">
        <h1 class="page-title mt-5">Browsed Recycled Clothes</h1>
        <div class="mb-0 mt-5">
            <a href="{{ route('add_item') }}" class="add-btn">Add New Item</a>
            <input type="text" id="searchInput" placeholder="Search clothes.." onkeyup="searchItems()">
        </div>
    </div>

    <!-- Cards Container -->
    <div class="cards-container" id="cardsContainer"></div>
    <div id="toast"></div>
    <div class="cards-container" id="cardsContainer"></div>
    <div id="swapModal" class="modal">
        <div class="modal-content">
            <span id="closeModal">&times;</span>
            <h2>Request Swap</h2>
            <input id="offerInput" min="1" type="number" placeholder="Enter offer">
            <p class="alert swap alert-danger d-none">is not valid offer</p>
            <button id="sendOffer">Send</button>
        </div>
    </div>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: false,
            mirror: true
        });
        const collectionsUrl = "{{ route('collections') }}";
        let isLoggedIn = {{ Auth::guard('user')->check() ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('assets/shared/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/shared/js/script.js') }}"></script>
    <script src="{{ asset('assets/shared/js/navbar.js') }}"></script>
</body>

</html>
