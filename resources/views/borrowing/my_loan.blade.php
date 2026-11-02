@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mx-auto" style="max-width: 1000px;">
        <h2 class="text-white mb-4">My Library Activity</h2>

        <!-- ACTIVE LOANS SECTION -->
        <h4 class="text-info mb-3"><i class="bi bi-book me-2"></i> Your Active Loans</h4>
        
        @if($activeLoans->count() > 0)
            <div class="row g-4 mb-5">
                @foreach($activeLoans as $loan)
                    <div class="col-md-6">
                        <div class="card bg-dark text-white border-secondary shadow h-100">
                            <div class="row g-0 h-100">
                                <div class="col-4 d-flex align-items-center justify-content-center p-3 border-end border-secondary">
                                    @if($loan->bookCopy->book->cover_path)
                                        <img src="{{ asset('storage/' . $loan->bookCopy->book->cover_path) }}" class="img-fluid rounded" alt="Cover" style="max-height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded w-100 h-100 d-flex align-items-center justify-content-center" style="min-height: 120px;">
                                            <span class="text-light small">No Cover</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold text-truncate" title="{{ $loan->bookCopy->book->title }}">
                                            {{ $loan->bookCopy->book->title }}
                                        </h5>
                                        <p class="card-text mb-1 small text-muted">Barcode: {{ $loan->bookCopy->barcode }}</p>
                                        
                                        @php
                                            $dueDate = \Carbon\Carbon::parse($loan->due_date);
                                            $isOverdue = $dueDate->isPast();
                                        @endphp
                                        
                                        <div class="mt-3 p-2 rounded {{ $isOverdue ? 'bg-danger text-white' : 'bg-secondary' }}">
                                            <strong>Due Date:</strong><br>
                                            {{ $dueDate->format('M d, Y') }} 
                                            @if($isOverdue)
                                                <span class="fw-bold">(OVERDUE)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- This matches the empty state from your previous screenshot -->
            <div class="card bg-dark border-secondary mb-5">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="text-white mt-3">No active loans</h5>
                    <p class="text-muted">You are not currently borrowing any books.</p>
                    <a href="{{ route('books.index') }}" class="btn btn-primary mt-2">Browse Catalog</a>
                </div>
            </div>
        @endif

        <!-- PAST LOANS HISTORY SECTION -->
        <h4 class="text-info mb-3"><i class="bi bi-clock-history me-2"></i> Borrowing History</h4>
        
        <div class="card bg-dark text-white border-secondary shadow-lg">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>BOOK</th>
                            <th>BORROWED ON</th>
                            <th>RETURNED ON</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pastLoans as $past)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $past->bookCopy->book->title }}</span><br>
                                    <small class="text-muted">ISBN: {{ $past->bookCopy->book->isbn }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($past->borrow_date)->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($past->return_date)->format('M d, Y') }}</td>
                                <td>
                                    @if($past->status == 'returned')
                                        <span class="badge bg-success">Returned</span>
                                    @elseif($past->status == 'lost')
                                        <span class="badge bg-danger">Lost</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No past borrowing history.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pastLoans->hasPages())
                <div class="card-footer bg-dark border-secondary pt-3">
                    {{ $pastLoans->links() }}
                </div>
            @endif
        </div>
        
    </div>
</div>
@endsection