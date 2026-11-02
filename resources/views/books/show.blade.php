@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="mx-auto" style="max-width: 1000px;">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="text-white m-0">Book Details</h2>
                <a href="{{ route('books.index') }}" class="btn btn-outline-secondary btn-sm">Back to Catalog</a>
            </div>

            <!-- Alerts -->
            @if (session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="row g-0">

                    <!-- Book Cover Container -->
                    <div class="col-md-4 p-4 text-center border-end border-secondary d-flex align-items-center justify-content-center">
                        @if ($book->cover_path)
                            <img src="{{ asset('storage/' . $book->cover_path) }}" class="img-fluid rounded shadow"
                                alt="Cover" style="max-height: 400px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center w-100 shadow"
                                style="max-width: 250px; height: 350px;">
                                <span class="text-light fs-4 fw-bold">No Cover</span>
                            </div>
                        @endif
                    </div>

                    <!-- Book Information Container -->
                    <div class="col-md-8 p-4 p-md-5">
                        <h2 class="fw-bold mb-2">{{ $book->title }}</h2>

                        <div class="mb-4">
                            <span class="badge bg-info text-dark me-2">{{ $book->category->name ?? 'Uncategorized' }}</span>
                            <span class="text-light">{{ $book->authors->pluck('name')->implode(', ') ?? 'Unknown Author' }}</span>
                        </div>

                        <ul class="list-group list-group-flush mb-4">
                            <li class="list-group-item bg-dark text-white border-secondary px-0">
                                <strong class="text-muted">ISBN:</strong> {{ $book->isbn }}
                            </li>
                            <li class="list-group-item bg-dark text-white border-secondary px-0">
                                <strong class="text-muted">Shelf Location:</strong>
                                {{ $book->shelf_location ?? 'Unassigned' }}
                            </li>
                            <li class="list-group-item bg-dark text-white border-secondary px-0">
                                <strong class="text-muted">Availability:</strong>
                                <span class="badge {{ ($book->available_copies ?? 0) > 0 ? 'bg-success' : 'bg-danger' }} ms-2">
                                    {{ $book->available_copies ?? 0 }} / {{ $book->copies_count ?? 0 }} Available
                                </span>
                            </li>
                        </ul>

                        <!-- ========================================== -->
                        <!-- MEMBER BORROWING SECTION                   -->
                        <!-- ========================================== -->
                        @if (auth()->user()->role_id == 3)
                            <div class="mt-4 p-4 border border-secondary rounded"
                                style="background-color: rgba(255, 255, 255, 0.03);">
                                <h5 class="text-info mb-3">Borrow this Book</h5>

                                @php
                                    // Check if the member already has an active request for this book
                                    $hasActiveRequest = \App\Models\BorrowingRequest::where('user_id', auth()->id())
                                        ->where('book_id', $book->id)
                                        ->whereIn('status', ['pending', 'approved'])
                                        ->exists();
                                @endphp

                                @if($hasActiveRequest)
                                    <button class="btn btn-warning btn-lg px-4 fw-bold shadow-sm text-dark" disabled>
                                        <i class="bi bi-hourglass-split me-2"></i> Request Pending
                                    </button>
                                    <small class="text-warning d-block mt-2">You have already requested this book. Please wait for a librarian to approve it.</small>
                                @elseif (($book->available_copies ?? 0) > 0)
                                    <form action="{{ route('member.request-book', $book->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg px-4 fw-bold shadow-sm">
                                            Request to Borrow
                                        </button>
                                    </form>
                                    <small class="text-muted d-block mt-2">By requesting this book, a librarian will prepare it for your pickup.</small>
                                @else
                                    <button class="btn btn-secondary btn-lg px-4 fw-bold shadow-sm" disabled>Currently Unavailable</button>
                                    <small class="text-danger d-block mt-2">All copies are currently checked out. Please check back later.</small>
                                @endif
                            </div>
                        @endif

                        <!-- ========================================== -->
                        <!-- ADMIN / LIBRARIAN ACTIONS                  -->
                        <!-- ========================================== -->
                        @if (auth()->user()->role_id != 3)
                            <div class="mt-4 pt-3 border-top border-secondary d-flex gap-2">
                                <a href="{{ route('books.edit', $book->id) }}" class="btn btn-primary">
                                    Edit Book Details
                                </a>

                                <form action="{{ route('copies.store', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success fw-bold">
                                        + Add 1 Copy to Inventory
                                    </button>
                                </form>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection