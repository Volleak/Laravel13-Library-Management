<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LibraryHere</title>
    
    <!-- Load the SCSS file through Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="login-page">

    <div class="login-card row g-0">
        <!-- Left Panel -->
            <div class="col-md-6 d-none d-md-block">
            <div class="left-panel">
                <div>
                    <h2 class="fw-bold mb-3">Simplify management with our dashboard</h2>
                    <p class="text-white-50">Simplify your library management with our user friendly admin dashboard.</p>
                </div>
                
                <div class="big-icon">
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div>

        <!-- Right Panel (Form) -->
        <div class="col-md-6 d-flex align-items-center">
            <div class="card-body p-5 w-100">
                <div class="text-center mb-4">
                    <h4 class="fw-bold"><i class="bi bi-layers-fill text-primary"></i> LibraryHere</h4>
                    <h3 class="fw-bold mt-3">Welcome Back</h3>
                    <p class="text-muted small">Please Login to your account</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger py-2">
                        <ul class="mb-0 list-unstyled small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3 position-relative">
                        <!-- Added id="password" for the JS script -->
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        <!-- Added id="togglePassword" for the JS script -->
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 text-muted" id="togglePassword" style="cursor: pointer; z-index: 10;"></i>
                    </div>

                    <div class="d-flex justify-content-end mb-4">
                        <a href="#" class="text-muted small text-decoration-none">Forgot Password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-4">Login</button>
                </form>

                <!-- New Register Link -->
                <div class="text-center">
                    <p class="text-muted small mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none">Register new</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script -->
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle the eye / eye-slash icon class
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>