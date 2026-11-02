@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mx-auto" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-white m-0">Member Profile</h2>
            <div>
                <a href="{{ route('members.edit', $member->id) }}" class="btn btn-primary btn-sm me-2">Edit Member</a>
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm">Back to Directory</a>
            </div>
        </div>

        <div class="card bg-dark text-white border-secondary shadow-lg">
            <div class="card-body p-4 text-center border-bottom border-secondary">
                @if($member->profile && $member->profile->photo_path)
                    <img src="{{ asset('storage/' . $member->profile->photo_path) }}" alt="Photo" class="rounded-circle mb-3 shadow" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-secondary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow" style="width: 120px; height: 120px;">
                        <span class="text-light fw-bold fs-1">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                    </div>
                @endif
                <h3 class="mb-1">{{ $member->name }}</h3>
                <p class="text-muted mb-2">{{ $member->email }}</p>
                
                @if($member->profile)
                    <span class="badge {{ $member->profile->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                        {{ ucfirst($member->profile->status) }}
                    </span>
                    <span class="badge bg-secondary ms-2">{{ $member->profile->barcode }}</span>
                @endif
            </div>
            
            <div class="card-body p-4">
                <h5 class="text-info mb-3">Detailed Information</h5>
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item bg-dark text-white border-secondary d-flex justify-content-between">
                        <strong class="text-muted">Phone Number</strong>
                        <span>{{ $member->profile->phone ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item bg-dark text-white border-secondary d-flex justify-content-between">
                        <strong class="text-muted">Date of Birth</strong>
                        <span>{{ $member->profile && $member->profile->dob ? \Carbon\Carbon::parse($member->profile->dob)->format('M d, Y') : 'N/A' }}</span>
                    </li>
                    <li class="list-group-item bg-dark text-white border-secondary d-flex justify-content-between">
                        <strong class="text-muted">Member Since</strong>
                        <span>{{ $member->created_at->format('M d, Y') }}</span>
                    </li>
                    <li class="list-group-item bg-dark text-white border-secondary">
                        <strong class="text-muted d-block mb-1">Address</strong>
                        <span>{{ $member->profile->address ?? 'No address provided.' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection