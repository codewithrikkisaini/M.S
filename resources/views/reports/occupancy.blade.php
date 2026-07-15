@extends('layouts.app')
@section('title', 'Occupancy Analytics')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $occupancyPercent }}<sup style="font-size: 20px">%</sup></h3>
                <p>Current Occupancy ({{ $occupiedRooms }}/{{ $totalRooms }} Rooms)</p>
            </div>
            <div class="icon"><i class="fas fa-chart-pie"></i></div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daily Occupancy Trend</h3></div>
            <div class="card-body">
                <canvas id="occupancyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('occupancyChart').getContext('2d');
    var occupancyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($labels) !!},
            datasets: [{
                label: 'Occupancy %',
                data: {!! json_encode($dailyOccupancy) !!},
                borderColor: '#ffc107',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true, max: 100 }
            }
        }
    });
</script>
@endpush
