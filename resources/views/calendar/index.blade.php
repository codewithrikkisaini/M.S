@extends('layouts.app')

@section('title', 'Booking Calendar')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reservations Calendar</h3>
    </div>
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/api/calendar/events',
            eventClick: function(info) {
                alert('Event: ' + info.event.title);
            }
        });
        calendar.render();
    });
</script>
@endpush
