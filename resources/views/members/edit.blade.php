@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mx-auto" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white m-0">Edit Member: {{ $member->name }}</h2>
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm">Back to Directory</a>
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
                <form action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h5 class="border-bottom border-secondary pb-2 mb-4 text-info">Account Details</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">Full Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $member->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $member->email) }}" required>
                        </div>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="alert alert-secondary py-2 small">Leave password fields blank if you do not want to change the current password.</div>
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

                    <h5 class="border-bottom border-secondary pb-2 mb-4 text-info">Member Profile</h5>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-light">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $member->profile->phone ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', $member->profile->dob ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-light">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $member->profile->address ?? '') }}</textarea>
                    </div>

                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label text-light">Member Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted d-block mt-1">Supported formats: JPG, PNG, WEBP.</small>
                            @if($member->profile && $member->profile->photo_path)
                                <div class="mt-2">
                                    <p class="mb-1 small text-light">Current Photo:</p>
                                    <img src="{{ asset('storage/' . $member->profile->photo_path) }}" alt="Current Photo" width="80" class="rounded">
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-light">Status</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $member->profile->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $member->profile->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-uppercase tracking-wide">Update Member</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection