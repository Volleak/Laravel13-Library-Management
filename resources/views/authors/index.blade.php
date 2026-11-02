@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Authors</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuthorModal">Add Author</button>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addAuthorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('authors.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">New Author</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><input type="text" name="name" class="form-control" placeholder="Author Name" required></div>
                        <div class="mb-3"><textarea name="bio" class="form-control" placeholder="Biography (Optional)" rows="3"></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Name</th><th>Biography</th><th>Total Books</th></tr>
                </thead>
                <tbody>
                    @forelse($authors as $author)
                    <tr>
                        <td class="fw-bold">{{ $author->name }}</td>
                        <td class="text-muted text-truncate" style="max-width: 300px;">{{ $author->bio ?? 'N/A' }}</td>
                        <td>{{ $author->books_count }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center py-4">No authors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3">{{ $authors->links() }}</div>
    </div>
</div>
@endsection