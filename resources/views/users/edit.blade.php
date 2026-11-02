@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mx-auto" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white m-0">Edit User: {{ $user->name }}</h2>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Back to Users</a>
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

        <div class="card bg-dark text-white border-secondary shadow-lg">
            <div class="card-body p-4 p-md-5">
                <!-- Added enctype to allow file uploads -->
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row mb-3">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-light">System Role</label>
                            <select name="role_id" class="form-select" required>
                                <option value="1" {{ old('role_id', $user->role_id) == '1' ? 'selected' : '' }}>Admin</option>
                                <option value="2" {{ old('role_id', $user->role_id) == '2' ? 'selected' : '' }}>Librarian</option>
                                <option value="3" {{ old('role_id', $user->role_id) == '3' ? 'selected' : '' }}>Member</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-secondary py-2 small">Leave password fields blank if you do not want to change the user's password.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">New Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>

                    <!-- NEW PHOTO FIELD & PREVIEW -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label text-light">Profile Photo (Optional)</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted d-block mt-1 mb-2">Supported formats: JPG, PNG, WEBP. Max size: 2MB.</small>
                            
                            @if($user->profile && $user->profile->photo_path)
                                <div class="mt-2 p-2 bg-secondary rounded d-inline-block">
                                    <p class="mb-1 small text-light fw-bold">Current Photo:</p>
                                    <img src="{{ asset('storage/' . $user->profile->photo_path) }}" alt="Current Photo" style="width: 80px; height: 80px; object-fit: cover;" class="rounded border border-dark">
                                </div>
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-uppercase tracking-wide">Update User</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection