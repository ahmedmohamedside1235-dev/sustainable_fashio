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
        <h1 class="page-title mt-5 mb-0">Browsed Recycled Clothes</h1>
        <div class="mb-0 mt-5">
            @if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'seller')
                <a href="{{ route('add_item') }}" class="add-btn">Add New Item</a>
            @endif
            <input type="text" id="searchInput" placeholder="Search clothes.." onkeyup="searchItems()">
        </div>
    </div>
    
    <!-- Cards Container -->
    <div class="container">
        <div class="cards-container" id="cardsContainer"></div>
    </div>
    <div id="toast"></div>

    <div id="editItemModal" class="modal">
        <div class="modal-content" style="max-width:400px;">
            <span onclick="closeEditItem()"
                style="position:absolute;top:10px;right:14px;font-size:18px;cursor:pointer;color:#888;">&times;</span>
            <h2 style="font-size:17px;margin-bottom:16px;">Edit Item</h2>

            <input type="hidden" id="editItemId">

            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:#666;display:block;margin-bottom:4px;">Price (EGP)</label>
                <input type="number" id="editPrice" min="1" step="0.01"
                    style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:9px;font-size:13px;outline:none;">
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:#666;display:block;margin-bottom:4px;">Condition</label>
                <select id="editCondition"
                    style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:9px;font-size:13px;outline:none;">
                    @foreach (\App\Models\Item_condition::all() as $c)
                        <option value="{{ $c->condition_id }}">{{ $c->condition_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:#666;display:block;margin-bottom:4px;">Material</label>
                <select id="editMaterial"
                    style="width:100%;padding:9px 12px;border:1px solid #ddd;border-radius:9px;font-size:13px;outline:none;">
                    @foreach (\App\Models\Material::all() as $m)
                        <option value="{{ $m->material_id }}">{{ $m->material_name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="font-size:12px;color:#666;display:block;margin-bottom:4px;">Image (optional)</label>
                <input type="file" id="editImage" accept="image/*"
                    style="width:100%;padding:7px 12px;border:1px solid #ddd;border-radius:9px;font-size:13px;">
            </div>

            <button onclick="saveEditItem()"
                style="width:100%;padding:10px;background:#198754;color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:600;cursor:pointer;">
                Save Changes
            </button>
        </div>
    </div>
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
        const addItemUrl       = "{{ route('add_item') }}";
    </script>
    <script src="{{ asset('assets/shared/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/shared/js/script.js') }}"></script>
    <script src="{{ asset('assets/shared/js/navbar.js') }}"></script>
</body>

</html>
