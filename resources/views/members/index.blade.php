@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-white m-0">Members Directory</h2>
        <a href="{{ route('members.create') }}" class="btn btn-primary">
            + Add New Member
        </a>
    </div>

    <div class="card bg-dark text-white border-secondary shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>EMAIL</th>
                        <th>BARCODE</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <!-- Profile Photo -->
                                    @if($member->profile && $member->profile->photo_path)
                                        <img src="{{ asset('storage/' . $member->profile->photo_path) }}" alt="Photo" class="rounded-circle me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                    @else
                                        <!-- Fallback Avatar -->
                                        <div class="bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <span class="text-light fw-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    
                                    <span class="fw-bold">{{ $member->name }}</span>
                                </div>
                            </td>
                            <td>{{ $member->email }}</td>
                            <td>
                                @if($member->profile && $member->profile->barcode)
                                    <span class="badge bg-secondary">{{ $member->profile->barcode }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- View Button -->
                                    <a href="{{ route('members.show', $member->id) }}" class="btn btn-sm btn-outline-info">
                                        View
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <!-- Delete Form -->
                                    <form action="{{ route('members.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to completely delete this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection