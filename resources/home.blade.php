@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
@php
    use App\Models\Appointment;
    use Carbon\Carbon;

    $appointments = \App\Models\Appointment::whereMonth('booking_date', \Carbon\Carbon::now()->month)
        ->whereYear('booking_date', \Carbon\Carbon::now()->year)
        ->where('status', 'Confirmed') // Only show confirmed appointments
        ->get();
@endphp
    <p>Welcome to Admin panel.</p>
    <div id="calendar"></div>

@php
    $events = [];
    foreach ($appointments as $appointment) {
        $events[] = [
            'title' => $appointment->name . ' - ' . $appointment->service->name,
            'start' => $appointment->booking_date . 'T' . $appointment->booking_time,
            'end' => $appointment->booking_date . 'T' . \Carbon\Carbon::parse($appointment->booking_time)->addHours(1)->format('H:i'),
            'description' => $appointment->notes,
            'color' => 'green',
        ];
    }
@endphp

<script>
    $(document).ready(function() {
        var calendarEvents = @json($events);
        $('#calendar').fullCalendar({
            events: calendarEvents,
            // Optional: Customization for the calendar
            eventClick: function(event) {
                // If you want to show more info when clicking on an event, you can do so here.
                alert(event.title);  // Display event title (name + service)
            }
        });
    });
</script>

@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    <!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.css" rel="stylesheet">

@stop

@section('js')
    <!-- jQuery and FullCalendar JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
@stop
