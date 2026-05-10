<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Requests – SwapSustain</title>
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}" type="image/png">
    <link href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="{{ asset('assets/request/css/request.css') }}">
</head>

<body>

    @include('shared.navBar')

    <!-- Banner -->
    <div class="page-banner">
        <h1><i class="fas fa-clipboard-list me-2"></i>My Requests</h1>
        @if (Auth::guard('user')->user()->role === 'seller')
            <p>Review incoming requests on your items and accept or reject them</p>
        @else
            <p>Track the status of items you've requested from the collections</p>
        @endif
    </div>

    <!-- Stats -->
    <div class="stats-bar">
        <div class="stat-box">
            <h2 id="statTotal">0</h2>
            <p>Total</p>
        </div>
        <div class="stat-box pending">
            <h2 id="statPending">0</h2>
            <p>Pending</p>
        </div>
        <div class="stat-box completed">
            <h2 id="statAccepted">0</h2>
            <p>Accepted</p>
        </div>
        <div class="stat-box cancelled">
            <h2 id="statRejected">0</h2>
            <p>Rejected</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-bar">
        <input type="text" id="filterSearch" placeholder="🔍 Search…" oninput="renderRequests()">
        <select id="filterStatus" onchange="renderRequests()">
            <option value="">All Status</option>
            <option value="pending">⏳ Pending</option>
            <option value="accepted">✅ Accepted</option>
            <option value="rejected">❌ Rejected</option>
        </select>
    </div>

    <!-- Grid -->
    <div class="req-grid" id="reqGrid"></div>

    <div id="toast"></div>

    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 600,
            once: true
        });
        let isLoggedIn = {{ Auth::guard('user')->check() ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('assets/shared/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/shared/js/navbar.js') }}"></script>
    <script src="{{ asset('assets/request/js/request.js') }}"></script>
</body>

</html>
