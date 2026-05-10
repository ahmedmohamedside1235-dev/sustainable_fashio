<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="sustainable fashion placemark">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sustainable Fashion</title>
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}">
    <link href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/Home/css/card.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/Home/css/home.css') }}">
</head>

<body>
    @if (session('errorLogin'))
        <div id="message" class="alert alert-success success text-light message my-4">
            {{ session('errorLogin') }}
        </div>
    @elseif (session('errorAdd'))
        <div id="message" class="alert alert-success success text-light message my-4">
            {{ session('errorAdd') }}
        </div>
    @endif
    {{-- nav bar --}}
    @include('shared.navBar')
    <!-- ===== HERO ===== -->
    <section class="hero-section">
        <div class="container hero-center">
            <h1 class="display-4 fw-bold ">
                Swap Your Clothes
            </h1>
            <p class="lead mb-4">
                Instead of buying new, swap with others and contribute to sustainable fashion
            </p>
            <a href="{{ route('collections') }}" class="btn btn-custom">
                Browse My Closet <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- ===== FEATURES ===== -->
    <div class="container my-5 p-4">
        <div class="row text-center g-4">
            <div class="col-md-4" data-aos="fade-right" data-aos-duration="1000">
                <div class="cardg feature-card p-3 h-100">
                    <i class="fas fa-hand-holding-heart fa-3x text-success m-3" style="color: #fff !important;"></i>
                    <h5>Safe Swap</h5>
                    <p class="text-light">Escrow system protects you until item arrives</p>

                </div>
            </div>
            <div class="col-md-4 " data-aos="fade-down" data-aos-duration="1000">
                <div class="cardg feature-card p-3 h-100">
                    <i class="fas fa-recycle fa-3x text-success m-3" style="color: #fff !important;"></i>
                    <h5>Sustainable Fashion</h5>
                    <p class="text-light"> Reduce your carbon footprint with every swap
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-left" data-aos-duration="1000">
                <div class="cardg feature-card p-3 h-100">
                    <i class="fas fa-users fa-3x text-success m-3" style="color: #fff !important;"></i>
                    <h5>Eco-Friendly community </h5>
                    <p class="text-light">Join a community that loves upcycling</p>
                </div>
            </div>
        </div>
    </div>
    <section class="about container my-5">

        <div class="row align-items-center g-5">
            <!-- TEXT -->
            <div class="col-md-6">
                <h2 class="mb-4" data-aos="fade-right">
                    About <span style="color:#198754;">SwapSustain</span>
                </h2>
                <div class="about-box" data-aos="fade-up">
                    <h5>🧭 Our Mission</h5>
                    <p>Reduce clothing waste and promote sustainable fashion through swapping and reuse.</p>
                </div>
                <div class="about-box" data-aos="fade-up" data-aos-delay="150">
                    <h5>♻️ What We Do</h5>
                    <p>We connect people who want to exchange clothes instead of buying new ones.</p>
                </div>
                <div class="about-box" data-aos="fade-up" data-aos-delay="300">
                    <h5>🌍 Why It Matters</h5>
                    <p>Fashion waste harms the environment — we help reduce it in a simple way.</p>
                </div>
            </div>
            <!-- IMAGE -->
            <div class="col-md-6 text-center" data-aos="zoom-in">
                <img src="{{ asset('assets/Home/images/homeAbout.jpg') }}" class="img-fluid rounded shadow about-img">
            </div>
        </div>
    </section>
    <section class="container my-5">
        <div class="text-center my-5">
            <h2 style="color: #2C5F2D;">Featured Items</h2>
            <p>Discover some of the latest shared pieces from our community.</p>
        </div>
        <div class="cards-wrapper">
            <div class="cards" id="cardss">

            </div>
        </div>
        <div class="learn-more">
            <a href="{{ route('collections') }}">Learn More →</a>
        </div>
    </section>
    <section data-aos="fade-up" class="cta" style="background-color: #198754 !important;">
        <h2>Ready to refresh your wardrobe? </h2>
        <p>Start swapping and make a difference today.</p>
        <button class="cta-btn" data-aos="zoom-in" data-aos-delay="200"><a
                style="text-decoration: none !important ; color: #2C5F2D !important;" href="{{route('add_item')}}">Add Your
                Item</a>
        </button>
    </section>
    <footer class="footer ">
        <div class="footer-container container">
            <!-- Logo / About -->
            <div class="footer-box">
                <h2>SwapSustain <img src="{{ asset('assets/shared/images/recycle-shirt.png') }}" alt=""></h2>
                <p>
                    Swap • Reuse • Reduce Waste
                    <br>
                    Join us in making fashion more sustainable.
                </p>
            </div>
            <!-- Quick Links -->
            <div class="footer-box">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Featured Items</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <!-- Contact -->
            <div class="footer-box">
                <h3>Contact</h3>
                <p>Email: info@swapsustain.com</p>
                <p>Phone: +20 11 000 000 00</p>
                <p>Location: Egypt</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 SwapSustain. All Rights Reserved.</p>
        </div>
    </footer>
    <script>
        const collectionsUrl = "{{ route('collections') }}";
        let isLoggedIn = {{ Auth::guard('user')->check() ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="{{ asset('assets/shared/js/jquery.js') }}"></script>
    <script src="{{ asset('assets/shared/js/script.js') }}"></script>
    <script src="{{ asset('assets/shared/js/navbar.js') }}"></script>
    <script src="{{ asset('assets/Home/js/home.js') }}"></script>
</body>

</html>
