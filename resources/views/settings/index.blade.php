@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="mx-auto" style="max-width: 800px;">
        
        <h2 class="text-white mb-4">Library Settings</h2>

        @if(session('success'))
            <div class="alert alert-success py-2">{{ session('success') }}</div>
        @endif
        
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
            <div class="card-header border-secondary pt-3 pb-2">
                <h5 class="mb-0 text-info"><i class="bi bi-gear me-2"></i> Borrowing Rules & Fines</h5>
            </div>
            
            <div class="card-body p-4">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-light">Default Loan Period (Days)</label>
                            <div class="input-group">
                                <input type="number" name="max_borrow_days" class="form-control bg-dark text-white border-secondary" 
                                    value="{{ $settings['max_borrow_days'] ?? 14 }}" required min="1" max="365">
                                <span class="input-group-text bg-secondary text-white border-secondary">Days</span>
                            </div>
                            <small class="text-muted">How long members can keep a book before it is overdue.</small>
                        </div>

                        <div class="col-md-6 mt-3 mt-md-0">
                            <label class="form-label text-light">Daily Overdue Fine</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-white border-secondary">$</span>
                                <input type="number" step="0.01" name="daily_fine_amount" class="form-control bg-dark text-white border-secondary" 
                                    value="{{ $settings['daily_fine_amount'] ?? 0.50 }}" required min="0">
                            </div>
                            <small class="text-muted">Amount charged per day when a book is late.</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-light">Maximum Books Per Member</label>
                            <div class="input-group">
                                <input type="number" name="max_books_per_member" class="form-control bg-dark text-white border-secondary" 
                                    value="{{ $settings['max_books_per_member'] ?? 3 }}" required min="1" max="50">
                                <span class="input-group-text bg-secondary text-white border-secondary">Books</span>
                            </div>
                            <small class="text-muted">Maximum active loans allowed at one time.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end border-top border-secondary pt-4 mt-2">
                        <button type="submit" class="btn btn-success fw-bold px-4">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection