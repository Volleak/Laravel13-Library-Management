@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h4 class="fw-bold mb-4">Borrowing Transactions</h4>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Book & Barcode</th>
                        <th>Borrow Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    <tr>
                        <td>{{ $tx->user->name }}</td>
                        <td>
                            <strong>{{ $tx->bookCopy->book->title }}</strong><br>
                            <small class="text-muted">{{ $tx->bookCopy->barcode }}</small>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($tx->borrow_date)->format('M d, Y') }}</td>
                        <td>
                            <span class="{{ \Carbon\Carbon::parse($tx->due_date)->isPast() && $tx->status === 'active' ? 'text-danger fw-bold' : '' }}">
                                {{ \Carbon\Carbon::parse($tx->due_date)->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $tx->status === 'active' ? 'primary' : ($tx->status === 'returned' ? 'success' : 'danger') }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td>
                            @if($tx->status === 'active')
                            <form action="{{ route('transactions.return', $tx) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Process Return</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4">No transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3">{{ $transactions->links() }}</div>
    </div>
</div>
@endsection