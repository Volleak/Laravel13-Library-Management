@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <h3 class="mb-4 fw-bold">Admin Overview</h3>

        <!-- Stats Row -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-normal mb-2">Total Members</h6>
                                <h3 class="fw-bold mb-0">{{ number_format($stats['total_members']) }}</h3>
                            </div>
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-normal mb-2">Active Loans</h6>
                                <h3 class="fw-bold mb-0 text-primary">{{ number_format($stats['active_loans']) }}</h3>
                            </div>
                            <div class="p-3 bg-primary bg-opacity-10 rounded-circle text-primary">
                                <i class="bi bi-book fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-normal mb-2">Overdue Books</h6>
                                <h3 class="fw-bold mb-0 text-danger">{{ number_format($stats['overdue_loans']) }}</h3>
                            </div>
                            <div class="p-3 bg-danger bg-opacity-10 rounded-circle text-danger">
                                <i class="bi bi-exclamation-triangle fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted fw-normal mb-2">Outstanding Fines</h6>
                                <h3 class="fw-bold mb-0 text-warning">${{ number_format($stats['outstanding_fines'], 2) }}
                                </h3>
                            </div>
                            <div class="p-3 bg-warning bg-opacity-10 rounded-circle text-warning">
                                <i class="bi bi-currency-dollar fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <!-- Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0">Borrowing Trends (6 Months)</h5>
                    </div>
                    <div class="card-body p-4">
                        <canvas id="borrowingChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                        <h5 class="fw-bold mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body p-4 d-flex flex-column gap-3">
                        <a href="{{ route('books.create') }}"
                            class="btn btn-primary text-start p-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="bi bi-plus-circle fs-5"></i> Add New Book
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-info w-100 mb-3 text-start">
                            <i class="bi bi-people-fill me-2"></i> Manage Users (Admin)
                        </a>
                        <a href="{{ route('members.create') }}"
                            class="btn btn-outline-secondary text-start p-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="bi bi-person-plus fs-5"></i> Register Member
                        </a>
                        <a href="#"
                            class="btn btn-outline-danger text-start p-3 rounded-3 d-flex align-items-center gap-3">
                            <i class="bi bi-receipt fs-5"></i> Process Fine Payment
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Table -->
        <div class="card border-0 shadow-sm">
            <div
                class="card-header bg-transparent border-0 pt-4 pb-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Recent Transactions</h5>
                <a href="#" class="btn btn-sm btn-link text-decoration-none">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Member</th>
                                <th>Book</th>
                                <th>Barcode</th>
                                <th>Borrow Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $transaction)
                                <tr>
                                    <td class="ps-4 fw-medium">{{ $transaction->user->name }}</td>
                                    <td>{{ $transaction->bookCopy->book->title }}</td>
                                    <td class="text-muted">{{ $transaction->bookCopy->barcode }}</td>
                                    <td>{{ \Carbon\Carbon::parse($transaction->borrow_date)->format('M d, Y') }}</td>
                                    <td>
                                        @if ($transaction->status === 'active')
                                            <span
                                                class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3">Active</span>
                                        @elseif($transaction->status === 'returned')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3">Returned</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('borrowingChart').getContext('2d');
            const chartData = @json($trendData);

            let chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Object.keys(chartData),
                    datasets: [{
                        label: 'Books Borrowed',
                        data: Object.values(chartData),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2563EB',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Update chart grid colors on theme change
            window.addEventListener('themeChanged', () => {
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                chart.options.scales.y.grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)';
                chart.update();
            });
        });
    </script>
@endpush
