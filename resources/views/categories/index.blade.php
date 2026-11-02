@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Manage Categories</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">Add Category</button>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">New Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" placeholder="e.g. Science Fiction" required>
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
                    <tr><th>Name</th><th>Slug</th><th>Total Books</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td class="text-muted">{{ $category->slug }}</td>
                        <td>{{ $category->books_count }}</td>
                        <td>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this category?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4">No categories found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3">{{ $categories->links() }}</div>
    </div>
</div>
@endsection