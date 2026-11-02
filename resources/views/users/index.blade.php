@extends('layouts.app')

@section('content')

    <!-- Floating Pop-out Notifications (Toasts) -->
    <div class="toast-container position-fixed top-0 end-0 p-4" style="z-index: 1055;">
        @if(session('success'))
            <div class="toast align-items-center text-white bg-success border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast align-items-center text-white bg-danger border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body fw-bold">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white m-0">User Management</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                + Add New User
            </a>
        </div>

        

        <!-- Search Form -->
        <div class="card mb-4 shadow-sm bg-dark border-secondary">
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card bg-dark text-white border-secondary shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>ROLE</th>
                            <th>JOINED</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- Profile Photo (if the user has one) -->
                                        @if ($user->profile && $user->profile->photo_path)
                                            <img src="{{ asset('storage/' . $user->profile->photo_path) }}" alt="Photo"
                                                class="rounded-circle me-3 shadow-sm"
                                                style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <!-- Fallback Avatar -->
                                            <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 40px; height: 40px;">
                                                <span
                                                    class="text-light fw-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif

                                        <span class="fw-bold">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span
                                        class="badge {{ $user->role_id == 1 ? 'bg-danger' : ($user->role_id == 2 ? 'bg-warning text-dark' : 'bg-info text-dark') }}">
                                        {{ $user->role->name ?? 'Unknown' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Edit
                                        </a>

                                        <!-- Delete Form -->
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No users found matching your
                                    criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Links (if you are using paginate(15) in your controller) -->
            @if ($users->hasPages())
                <div class="card-footer bg-dark border-secondary pt-3">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Script to auto-show the pop-out message -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Find any toasts on the page
            var toastElements = document.querySelectorAll('.toast');
            
            // Initialize and show each toast
            toastElements.forEach(function (toastEl) {
                // delay: 3000 means it will pop out and disappear after 3 seconds
                var toast = new bootstrap.Toast(toastEl, { delay: 3000 });
                toast.show();
            });
        });
    </script>
@endsection
