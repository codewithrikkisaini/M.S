@extends('layouts.app')
@section('title', 'Daily Operations Report')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Date</h3>
        <div class="card-tools">
            <form method="GET" action="{{ route('reports.daily') }}" class="d-flex">
                <input type="date" name="date" class="form-control form-control-sm me-2" value="{{ $date }}">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3"><strong>Occupied Rooms:</strong> {{ $occupiedRooms }}</div>
            <div class="col-md-3"><strong>Available Rooms:</strong> {{ $availableRooms }}</div>
            <div class="col-md-3"><strong>Reserved Rooms:</strong> {{ $reservedRooms }}</div>
            <div class="col-md-3"><strong>Maintenance Rooms:</strong> {{ $maintenanceRooms }}</div>
        </div>
        <hr>
        <div class="row mt-3">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-sign-in-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Check-Ins</span>
                        <span class="info-box-number">{{ $checkInsToday }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-sign-out-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Check-Outs</span>
                        <span class="info-box-number">{{ $checkOutsToday }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Revenue</span>
                        <span class="info-box-number">${{ number_format($revenueToday, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
