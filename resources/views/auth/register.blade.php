<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - LibraryHere</title>
    
   
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
                    <h2 class="fw-bold mb-3">Join LibraryHere today</h2>
                    <p class="text-white-50">Unlock a world of knowledge and easily manage your physical book borrowing history.</p>
                </div>
                
                <div class="big-icon">
                    <i class="bi bi-journal-bookmark-fill"></i>
                </div>
            </div>
        </div>

        <!-- Right Panel (Form) -->
        <div class="col-md-6 d-flex align-items-center">
            <div class="card-body p-5 w-100">
                <div class="text-center mb-4">
                    <h4 class="fw-bold"><i class="bi bi-layers-fill text-primary"></i> LibraryHere</h4>
                    <h3 class="fw-bold mt-3">Create an Account</h3>
                    <p class="text-muted small">Fill in your details to get started</p>
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

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" value="{{ old('name') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
                    </div>

                    <div class="mb-3 position-relative">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" data-target="password" style="cursor: pointer; z-index: 10;"></i>
                    </div>

                    <div class="mb-4 position-relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 text-muted toggle-password" data-target="password_confirmation" style="cursor: pointer; z-index: 10;"></i>
                    </div>

                    <div class="mb-3">
                    <select name="role" class="form-control form-select" required>
                        <option value="" disabled selected>Select your role</option>
                        <option value="Admin">Admin</option>
                        <option value="Librarian">Librarian</option>
                        <option value="Member">Member</option>
                    </select>
</div>

                    <button type="submit" class="btn btn-primary w-100 mb-4">Register</button>
                </form>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-muted small mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Toggle Script (Updated for multiple fields) -->
    <script>
        const toggleIcons = document.querySelectorAll('.toggle-password');

        toggleIcons.forEach(icon => {
            icon.addEventListener('click', function () {
                // Find the specific input this icon belongs to
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the eye / eye-slash icon class
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>