@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="text-white mb-4">Borrowing Requests</h2>
    
    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2">{{ session('error') }}</div>
    @endif
    
    <!-- ADD THIS MISSING ERROR BLOCK -->
    @if($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 list-unstyled fw-bold">
                @foreach($errors->all() as $error)
                    <li><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card bg-dark text-white border-secondary shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Member</th>
                        <th>Book Requested</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td>
                            <span class="fw-bold">{{ $req->user->name }}</span>
                        </td>
                        <td>
                            <span class="text-info">{{ $req->book->title }}</span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $req->status === 'pending' ? 'warning text-dark' : ($req->status === 'fulfilled' || $req->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>
                        <td>
                            <!-- CORRECTED PERMISSION CHECK HERE -->
                            @if($req->status === 'pending' && auth()->user()->role_id != 3)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-success fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal{{ $req->id }}">
                                        Approve
                                    </button>
                                    <form action="{{ route('requests.reject', $req) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                </div>

                                <!-- Dark Theme Approve Modal -->
                                <div class="modal fade" id="approveModal{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content bg-dark text-white border-secondary">
                                            <form action="{{ route('requests.approve', $req) }}" method="POST">
                                                @csrf
                                                <div class="modal-header border-secondary">
                                                    <h5 class="modal-title text-success">Approve & Issue Book</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-4">Issuing <strong class="text-info">{{ $req->book->title }}</strong> to <strong>{{ $req->user->name }}</strong>.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label text-light">Scan or Enter Physical Barcode</label>
                                                        <!-- Input styled for dark mode -->
                                                        <input type="text" name="barcode" class="form-control bg-dark text-white border-secondary" required placeholder="e.g. BK-0012-CPY-001">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-secondary">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success fw-bold">Issue Book</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            No requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
            <div class="card-footer bg-dark border-secondary pt-3">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection