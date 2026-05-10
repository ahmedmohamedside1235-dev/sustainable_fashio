<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets/addItem/css/add.css') }}">
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg">
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
                                <li><a class="dropdown-item text-black alert-success" href="{{ route('register') }}">Add
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- PAGE -->
    <div class="page-wrapper">
        @if (session('success'))
            <div id="message" class="alert alert-success success text-light message my-4">
                {{ session('success') }}
            </div>
        @endif
        <div class="card-wrapper">
            <!-- LEFT -->
            <div class="left-panel">
                <div>
                    <span class="left-tag">NEW LISTING</span>
                    <div class="left-title">List your item &amp; give it a new life</div>
                    <div class="left-sub">Add your pre-loved fashion pieces and connect with buyers who care about
                        sustainable style.</div>

                    <div class="upload-zone" onclick="document.getElementById('imageInput').click()">
                        <div class="upload-icon">📷</div>
                        <div class="upload-txt">Click to upload photo</div>
                        <div class="upload-sub">PNG, JPG up to 5MB</div>
                    </div>

                    <div class="img-placeholder" id="imgPlaceholder">👕</div>
                    <img id="previewImg" src="" alt="preview">
                </div>

                <div class="tips">
                    <div class="tip-title">TIPS FOR A GREAT LISTING</div>
                    <div class="tip-item">
                        <div class="tip-dot"></div> Use natural lighting for photos
                    </div>
                    <div class="tip-item">
                        <div class="tip-dot"></div> Be honest about condition
                    </div>
                    <div class="tip-item">
                        <div class="tip-dot"></div> Set a fair price for quick swap
                    </div>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="right-panel">
                <div class="form-title">Item details</div>
                <div class="form-sub">Fill in the details below to publish your listing</div>
                <form action="{{ route('add_item_submit') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- hidden file input triggered by upload zone -->
                    <input type="file" id="imageInput" name="image" accept="image/*" style="display:none"
                        onchange="previewFile(event)">
                    @error('image')
                        <p class="text-danger small mt-1">{{ $message }}</p>
                    @enderror
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="field-label">PRICE ( EGP , EUP ($) )</label>
                            <input type="number" name="price" step="0.01" min="0" class="form-control"
                                placeholder="0.00" value="{{ old('price') }}" required>
                            @error('price')
                                <p class="text-danger small mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="field-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="" {{ old('status') == '' ? 'selected' : '' }} hidden>Select...
                                </option>
                                <option value="new" {{ old('status') == 'new' ? 'selected' : '' }}>New
                                </option>
                                <option value="good" {{ old('status') == 'good' ? 'selected' : '' }}>Good
                                </option>
                                <option value="fair" {{ old('status') == 'fair' ? 'selected' : '' }}>Fair
                                </option>
                            </select>
                            @error('status')
                                <p class="text-danger small mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="field-label">CATEGORY</label>
                        <select name="category" class="form-select" required>
                            <option value="" {{ old('category') == '' ? 'selected' : '' }} hidden>Select
                                category...</option>
                            <option value="shirts" {{ old('category') == 'shirts' ? 'selected' : '' }}>Shirts
                            </option>
                            <option value="pants" {{ old('category') == 'pants' ? 'selected' : '' }}>Pants
                            </option>
                            <option value="dresses" {{ old('category') == 'dresses' ? 'selected' : '' }}>Dresses
                            </option>
                            <option value="jackets" {{ old('category') == 'jackets' ? 'selected' : '' }}>Jackets
                            </option>
                        </select>
                        @error('category')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="field-label">MATERIAL</label>
                        <select name="material" class="form-select" required>
                            <option value="" {{ old('material') == '' ? 'selected' : '' }} hidden>Select
                                material...</option>
                            <option value="cotton" {{ old('material') == 'cotton' ? 'selected' : '' }}>
                                Cotton
                            </option>
                            <option value="polyester" {{ old('material') == 'polyester' ? 'selected' : '' }}>
                                Polyester
                            </option>
                            <option value="wool" {{ old('material') == 'wool' ? 'selected' : '' }}>
                                Wool
                            </option>
                            <option value="denim" {{ old('material') == 'denim' ? 'selected' : '' }}>
                                Denim
                            </option>
                            <option value="leather" {{ old('material') == 'leather' ? 'selected' : '' }}>
                                Leather
                            </option>
                            <option value="silk" {{ old('material') == 'silk' ? 'selected' : '' }}>
                                Silk
                            </option>
                            <option value="linen" {{ old('material') == 'linen' ? 'selected' : '' }}>
                                Linen
                            </option>
                            <option value="nylon" {{ old('material') == 'nylon' ? 'selected' : '' }}>
                                Nylon
                            </option>
                        </select>
                        @error('material')
                            <p class="text-danger small mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">Publish listing</button>
                </form>
            </div>

        </div>
    </div>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/addItem/js/add.js') }}"></script>
    <script src="{{ asset('assets/shared/js/navbar.js') }}"></script>
    <script>
        const message = document.getElementById('message');
        if (message) {
            setTimeout(() => {
                message.classList.add('show');
            }, 100);

            setTimeout(() => {
                message.classList.remove('show');
            }, 2000);
        }
    </script>
</body>

</html>
