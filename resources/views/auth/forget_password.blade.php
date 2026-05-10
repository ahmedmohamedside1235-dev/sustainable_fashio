<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('assets/shared/css/plugins/bootstrap.css') }}">
    <link href="{{ asset('assets/forget_password/css/forget.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card forgot-card">
                    <div class="card-header">
                        <i class="fas fa-key"></i>
                        <h3>Forgot Password?</h3>
                        <p>Don't worry, it happens to the best of us</p>
                    </div>
                    <div class="card-body">
                        <!-- Success Message -->
                        <div id="successAlert" class="alert alert-success d-none" role="alert">
                            <i class="fas fa-check-circle"></i>
                            <strong>Reset link sent!</strong> Check your email inbox.
                        </div>
                        <!-- Error Message -->
                        <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Email not found!</strong> Please try again.
                        </div>
                        <!-- Info Text -->
                        <p class="text-muted text-center mb-4">
                            <i class="fas fa-envelope"></i> Enter your email address and we'll send you
                            a link to reset your password.
                        </p>
                        <!-- Forgot Password Form -->
                        <form id="forgotPasswordForm">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <input type="email" class="form-control" id="email"
                                    placeholder="Enter your registered email" required>
                                <small class="text-muted mt-1 d-block">
                                    We'll send a password reset link to this email
                                </small>
                            </div>
                            <button type="submit" class="btn btn-send mb-3">
                                <i class="fas fa-paper-plane"></i> Send Reset Link
                            </button>
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <button type="button" class="btn btn-back" onclick="goToLogin()">
                                    <i class="fas fa-arrow-left"></i> Back to Login
                                </button>
                            </a>
                        </form>
                        <hr class="my-4">
                        <div class="text-center">
                            <p class="mb-0">
                                Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none fw-bold mainColor">
                                    Register<i class="fas fa-arrow-right"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>
</body>

</html>
