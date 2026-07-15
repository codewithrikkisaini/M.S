@extends('layouts.app')
@section('title', 'Revenue Analytics')

@section('content')
<div class="row">
    <div class="col-md-3 col-6">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Today</span>
                <span class="info-box-number">${{ number_format($revenueToday, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="info-box bg-info">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Week</span>
                <span class="info-box-number">${{ number_format($revenueWeek, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="info-box bg-primary">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Month</span>
                <span class="info-box-number">${{ number_format($revenueMonth, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="info-box bg-dark">
            <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Year</span>
                <span class="info-box-number">${{ number_format($revenueYear, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Monthly Revenue Trend</h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <button class="btn btn-sm btn-outline-success"><i class="fas fa-file-excel"></i> Export Excel</button>
        </div>
    </div>
    <div class="card-body">
        <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Revenue ($)',
                data: {!! json_encode($monthlyRevenue) !!},
                backgroundColor: '#28a745',
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endpush
