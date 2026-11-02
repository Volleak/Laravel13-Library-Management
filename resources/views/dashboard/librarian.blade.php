@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <h2 class="text-white mb-4">Librarian Dashboard</h2>
    
    <!-- Your existing Top Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-dark text-white border-secondary shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Books Issued Today</h6>
                    <h3 class="text-primary mb-0">{{ $issuedToday ?? 2 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-dark text-white border-secondary shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Books Returned Today</h6>
                    <h3 class="text-success mb-0">{{ $returnedToday ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- NEW: Weekly Activity Graph -->
    <div class="card bg-dark border-secondary shadow-lg">
        <div class="card-body">
            <h5 class="text-white mb-4">Weekly Borrowing Activity</h5>
            <!-- Canvas container for the chart -->
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="weeklyActivityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script (Requires internet connection to load) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('weeklyActivityChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates ?? []) !!},
                datasets: [
                    {
                        label: 'Books Issued',
                        data: {!! json_encode($issuedData ?? []) !!},
                        borderColor: '#0d6efd', // Bootstrap Primary Blue
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4 // Smooth curves
                    },
                    {
                        label: 'Books Returned',
                        data: {!! json_encode($returnedData ?? []) !!},
                        borderColor: '#198754', // Bootstrap Success Green
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#f8f9fa' } // White text for dark mode
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#adb5bd' },
                        grid: { color: '#343a40' } // Darker grid lines
                    },
                    y: {
                        ticks: { color: '#adb5bd', stepSize: 1 },
                        grid: { color: '#343a40' },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection