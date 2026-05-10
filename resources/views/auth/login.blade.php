<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/login/css/login.css') }}">
    <title>Login</title>
</head>

<body>

    @if (session('errorLogin'))
        <div id="message" class="alert alert-danger danger text-light message my-4">
            {{ session('errorLogin') }}
        </div>
    @endif
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h3>Login</h3>
                        <p class="mb-0">Welcome to the team dashboard</p>
                    </div>
                    <div class="card-body p-4  position-relative">
                        @if (session('success'))
                            <div id="message" class="alert alert-success success text-light message my-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('invalid'))
                            <div class="alert alert-danger danger text-light my-4">
                                {{ session('invalid') }}
                            </div>
                        @endif

                        <!-- Form -->
                        <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Email address <span class="mainColor fs-4">*</span>
                                </label>
                                <input value="{{ old('email') }}" name="email" type="email" class="form-control"
                                    id="email" required>
                                @error('email')
                                    <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Password <span class="mainColor fs-4">*</span>
                                </label>
                                <input value="{{ old('password') }}" name="password" type="password"
                                    class="form-control" id="password" required>
                                @error('password')
                                    <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        <hr class="my-4">
                        <div class="text-center">
                            <a color="black" href="{{ route('forget') }}"
                                class="mainColor fw-bolder text-decoration-none">Forgot
                                password?</a>
                            <span class="mx-2">|</span>
                            <a href="{{ route('register') }}" class="text-decoration-none mainColor fw-bolder">Create
                                new
                                account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/login/js/login.js') }}"></script>
    <script>
        const message = document.getElementById('message');
        if (message) {
            setTimeout(() => {
                message.classList.add('show');
            }, 100);

            setTimeout(() => {
                message.classList.remove('show');
            }, 4000);
        }
    </script>
</body>

</html>
