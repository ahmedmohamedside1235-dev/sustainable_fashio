<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('assets/shared/images/recycle-shirt.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/register/css/register.css') }}">
    <title>Register</title>
</head>

<body>
    @if (session('success'))
        <div id="message" class="alert alert-success success text-light message my-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="register w-100">
        <div class="container reg">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card login-card">
                        <h3 class="text-center head py-4 mb-0">Register</h3>
                        @if (session('error'))
                            <p class="alert alert-danger my-4">{{ session('error') }}</p>
                        @endif
                        <div class="card-body py-2">
                            <form action="{{ route('register.submit') }}" method="POST" id="loginForm">
                                @csrf
                                <div class="from-group mb-3">
                                    <label for="Type" class="mb-2">Type <span
                                            class="mainColor fs-4">*</span></label>
                                    <select id="Type" required name="role" class="form-control" required>
                                        <option value="" {{ old('role') == '' ? 'selected' : '' }} hidden>
                                        </option>
                                        @if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 'admin')
                                            <option {{ old('role') == 'admin' ? 'selected' : '' }} value="admin">Admin
                                            </option>
                                        @else
                                            <option {{ old('role') == 'seller' ? 'selected' : '' }} value="seller">
                                                Seller
                                            </option>
                                            <option {{ old('role') == 'buyer' ? 'selected' : '' }} value="buyer">Buyer
                                            </option>
                                        @endif
                                    </select>
                                    @error('role')
                                        <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i> Username <span class="mainColor fs-4">*</span>
                                    </label>
                                    <input value="{{ old('name') }}" name="name" type="text"
                                        class="form-control" id="username" required>
                                    @error('name')
                                        <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>
                                    @enderror

                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i> Email address <span class="mainColor fs-4">*</span>
                                    </label>
                                    <input value="{{ old('email') }}" name="email" type="email"
                                        class="form-control" id="email" required>
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

                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i> phone <span class="mainColor fs-4">*</span>
                                    </label>
                                    <input value="{{ old('phone') }}" name="phone" type="text"
                                        class="form-control" id="phone" required>
                                    @error('phone')
                                        <p class="alert alert-danger fs-6 mt-3">{{ $message }}</p>
                                    @enderror
                                </div>


                                <button type="submit" class="btn btn-login">
                                    <i class="fas fa-sign-in-alt"></i> Sign Up
                                </button>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <a href="{{ route('login') }}" class="text-decoration-none">
                                    <i class="fas fa-key"></i>
                                    <span class="text-black">Already have an account ?</span>
                                    <span class="mainColor fw-bolder">log in</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/shared/js/bootstrap.js') }}"></script>

</body>

</html>
