<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LibraryHere') }}</title>

    <!-- Prevent theme flashing -->
    <script>
        const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ?
            'dark' : 'light');
        document.documentElement.setAttribute('data-theme', savedTheme);
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>

<body>
    @auth
        <!-- Sidebar -->
        <aside class="sidebar d-flex flex-column p-3">
            <div class="d-flex align-items-center mb-4 px-2">
                <i class="bi bi-book-half fs-4 text-primary me-2"></i>
                <h4 class="mb-0 fw-bold">LibraryHere</h4>
            </div>

            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid me-2"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('books.index') }}"
                        class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text me-2"></i> Catalog
                    </a>
                </li>
                @if (auth()->user()->role_id != 3)
                    <li class="nav-item">
                        <a href="{{ route('requests.index') }}"
                            class="nav-link text-white {{ request()->routeIs('requests.*') ? 'active' : '' }}">
                            <i class="bi bi-inbox me-2"></i> Borrow Requests
                        </a>
                    </li>
                @endif

                @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Librarian'))
                    <li class="nav-item">
                        <a href="{{ route('members.index') }}"
                            class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                            <i class="bi bi-people me-2"></i> Members
                        </a>
                    </li>
                @endif
                @if (auth()->user()->role->name === 'Admin')
                    <a href="{{ route('users.index') }}"
                        class="nav-link text-white mb-2 {{ request()->routeIs('users.*') ? 'bg-primary rounded' : '' }}">
                        <i class="bi bi-person-badge-fill me-2"></i> Users
                    </a>
                @endif

            </ul>

            <hr class="border-secondary">

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle nav-link p-2"
                    id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563EB&color=fff"
                        alt="" width="32" height="32" class="rounded-circle me-2">
                    <strong>{{ auth()->user()->name }}</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">Sign out</button>
                        </form>
                    </li>
                </ul>
            </div>
        </aside>
    @endauth

    <!-- Main Content Wrapper -->
    <main class="{{ auth()->check() ? 'main-content' : 'd-flex justify-content-center align-items-center vh-100' }}">

        @auth
            <!-- Top Navbar for Mobile Toggle -->
            <header class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom d-lg-none">
                <button id="sidebar-toggle" class="btn btn-outline-secondary border-0"><i
                        class="bi bi-list fs-4"></i></button>
                <h5 class="mb-0">LibraryHere</h5>
                <!-- Invisible placeholder to keep the header title centered now that the moon icon is gone -->
                <div style="width: 42px;"></div>
            </header>
        @endauth

        <!-- Toast Notifications -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-4 border-success"
                role="alert">
                <i class="bi bi-check-circle-fill me-2 text-success"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-4 border-danger"
                role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>