@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h4 class="fw-bold mb-4">Manage Fines</h4>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Book</th>
                        <th>Total Fine</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fines as $fine)
                    <tr>
                        <td>{{ $fine->borrowingTransaction->user->name }}</td>
                        <td>{{ $fine->borrowingTransaction->bookCopy->book->title }}</td>
                        <td class="fw-bold">${{ number_format($fine->amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $fine->status === 'paid' ? 'success' : ($fine->status === 'unpaid' ? 'danger' : 'warning') }}">
                                {{ ucfirst($fine->status) }}
                            </span>
                        </td>
                        <td>
                            @if(in_array($fine->status, ['unpaid', 'partial']))
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#payModal{{ $fine->id }}">Record Payment</button>

                            <!-- Payment Modal -->
                            <div class="modal fade" id="payModal{{ $fine->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('fines.pay', $fine) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Process Fine Payment</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Payment Amount ($)</label>
                                                    <input type="number" step="0.01" name="amount" class="form-control" value="{{ $fine->amount }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Payment Method</label>
                                                    <select name="payment_method" class="form-select" required>
                                                        <option value="cash">Cash</option>
                                                        <option value="card">Credit Card</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success">Submit Payment</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4">No fines found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white pt-3">{{ $fines->links() }}</div>
    </div>
</div>
@endsection