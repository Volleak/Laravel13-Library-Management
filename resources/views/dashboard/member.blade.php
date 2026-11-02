@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Welcome back, {{ auth()->user()->name }}!</h3>
                <p class="text-muted mb-0">Here is your current library activity.</p>
            </div>
        </div>

        @if ($fines > 0)
            <div
                class="alert alert-warning border-0 border-start border-4 border-warning shadow-sm d-flex align-items-center mb-4">
                <i class="bi bi-exclamation-circle-fill fs-4 me-3 text-warning"></i>
                <div>
                    <strong>You have outstanding fines totaling ${{ number_format($fines, 2) }}</strong>
                    <p class="mb-0 small">Please visit the library desk to settle your balance.</p>
                </div>
            </div>
        @endif

        <h5 class="fw-bold mb-3">Your Active Loans</h5>
        <div class="row g-4 mb-5">
            @foreach ($activeLoans as $loan)
                <div class="col-md-4 mb-4">
                    <div class="card bg-dark text-white border-secondary shadow-sm h-100">
                        <div class="card-body d-flex gap-3">

                            <!-- Book Cover (Matches your current white placeholder style) -->
                            <div class="flex-shrink-0">
                                @if ($loan->bookCopy->book->cover_path)
                                    <img src="{{ asset('storage/' . $loan->bookCopy->book->cover_path) }}"
                                        class="rounded shadow-sm" alt="Cover"
                                        style="width: 70px; height: 95px; object-fit: cover;">
                                @else
                                    <div class="bg-white rounded d-flex align-items-center justify-content-center shadow-sm"
                                        style="width: 70px; height: 95px;">
                                        <i class="bi bi-book text-muted fs-2"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Book Information & New Button -->
                            <div class="flex-grow-1 d-flex flex-column">
                                <h6 class="fw-bold mb-1 text-truncate" title="{{ $loan->bookCopy->book->title }}">
                                    {{ $loan->bookCopy->book->title }}
                                </h6>
                                <small class="text-muted mb-2">Barcode: {{ $loan->bookCopy->barcode }}</small>

                                <div>
                                    <span class="badge rounded-pill border border-success text-success px-3 py-2 mb-3">
                                        Due: {{ \Carbon\Carbon::parse($loan->due_date)->format('M d, Y') }}
                                    </span>
                                </div>

                                <!-- NEW: View Book Button -->
                                <div class="mt-auto">
                                    <a href="{{ route('books.show', $loan->bookCopy->book->id) }}"
                                        class="btn btn-sm btn-outline-info w-100">
                                        View Book Details
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
