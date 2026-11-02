@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Book Catalog</h2>
            
            <!-- Only Admins and Librarians can see the Add button -->
            @if(auth()->user()->role_id != 3)
                <a href="{{ route('books.create') }}" class="btn btn-primary">Add New Book</a>
            @endif
        </div>

        <!-- DB-Backed Search & Filter Form -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('books.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search title or ISBN..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>ISBN</th>
                            <th>Category</th>
                            <th>Authors</th>
                            <th>Availability</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($books as $book)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <!-- Display the thumbnail -->
                                        @if($book->cover_path)
                                            <img src="{{ asset('storage/' . $book->cover_path) }}" alt="Cover" class="rounded me-3" style="width: 45px; height: 65px; object-fit: cover;">
                                        @else
                                            <!-- Fallback if no image is uploaded -->
                                            <div class="bg-secondary rounded me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 65px;">
                                                <span class="text-light small">N/A</span>
                                            </div>
                                        @endif

                                        <!-- Book Title and Location -->
                                        <div>
                                            <span class="fw-bold">{{ $book->title }}</span><br>
                                            <small class="text-muted" style="font-size: 0.8rem;">Loc: {{ $book->shelf_location ?? 'Unassigned' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $book->isbn }}</td>
                                <td><span class="badge bg-info text-dark">{{ $book->category->name ?? 'N/A' }}</span></td>
                                <td>{{ $book->authors->pluck('name')->implode(', ') }}</td>
                                <td>
                                    <span class="badge {{ $book->available_copies > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $book->available_copies }} / {{ $book->copies_count }} Available
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- View Button (Visible to Everyone) -->
                                        <a href="{{ route('books.show', $book->id) }}" class="btn btn-sm btn-outline-info">
                                            View
                                        </a>

                                        <!-- Edit and Delete (Visible to Admins and Librarians only) -->
                                        @if(auth()->user()->role_id != 3)
                                            <a href="{{ route('books.edit', $book->id) }}" class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this book?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No books found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white pt-3">
                {{ $books->links() }} <!-- Pagination -->
            </div>
        </div>
    </div>
@endsection