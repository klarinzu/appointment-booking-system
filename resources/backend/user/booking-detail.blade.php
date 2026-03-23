@extends('adminlte::page')

@section('title', 'Booking Detail')

@section('content_header')
    <div class="row ">
        <div class="col-sm-6">
            <h1><a href="{{ route('employee.bookings') }}" class="btn btn-sm btn-primary">Back</a></h1>
        </div>
    </div>
@stop

@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">


                <div class="invoice p-3 mb-3">

                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> {{ $setting->bname }}
                                <small class="float-right">Date: {{ $booking->created_at->format('d, M Y') }}</small>
                            </h4>
                        </div>

                    </div>

                    <div class="row invoice-info pb-3">
                        <div class="col-sm-4 invoice-col">
                            From
                            <address>
                                <strong>{{ $setting->bname }}</strong><br>
                                Email: {{ $setting->email }} <br>
                                Phone: {{ $setting->phone }} <br>
                                {{ $setting->address }}

                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            To
                            <address>
                                <strong>{{ $booking->name }}</strong><br>
                                Email: {{ $booking->email }} <br>
                                Phone: {{ $booking->phone }} <br>
                            </address>
                        </div>

                        <div class="col-sm-4 invoice-col">
                            <b>Booking ID: </b> #{{ $booking->booking_id }}<br>
                            <br>
                            <b>Created:</b> {{ $booking->created_at->format('d M Y h:i A') }}<br>
                            <b>Status:</b> <span class="badge badge-info">{{ $booking->status }}</span><br>
                            <b>Customer Type:</b> {{ $booking->user ? 'Registered user' : 'Guest booking' }}

                        </div>

                    </div>


                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Service</th>
                                        <th>Assigned Staff</th>
                                        <th>Booking Time</th>
                                        <th>Booking Date</th>
                                        <th>Notes</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $booking->name }}</td>
                                        <td>{{ $booking->phone }}</td>
                                        <td>{{ $booking->service?->title ?? 'N/A' }}</td>
                                        <td>{{ $booking->employee?->user?->name ?? 'Unassigned' }}</td>
                                        <td>{{ $booking->booking_time }}</td>
                                        <td>{{ $booking->booking_date ? date('d M Y', strtotime($booking->booking_date)) : 'N/A' }}
                                        </td>
                                        <td>{{ $booking->notes ?: 'N/A' }}</td>
                                        <td>{{ number_format($booking->amount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="row">

                        <div class="col-10">


                            <p class="text-muted well well-sm shadow-none" style="margin-top: 10px;">
                                <b>Email:</b> {{ $booking->email }} | <b>Phone:</b> {{ $booking->phone }}
                            </p>
                            <p class="lead mb-0"><b>Service Provider</b>: {{ $booking->employee?->user?->name ?? 'Unassigned' }}</p>
                            <p class="lead mb-0"><b>Booking Time</b>: {{ $booking->booking_time }}</p>
                            <p class="lead"><b>Booking Date</b>:
                                {{ $booking->booking_date ? date('d M Y', strtotime($booking->booking_date)) : 'N/A' }}</p>

                        </div>

                        <div class="col-2">

                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>

                                        <tr>
                                            <th>Total:</th>
                                            <td>{{ number_format($booking->amount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>


                    <div class="row no-print">
                        <div class="col-12">
                            <a href="#" onclick="window.print()" class="btn btn-default"><i class="fas fa-print"></i> Print</a>




                            {{-- <a href="{{ route('bookings.generate-pdf', ['id' => $booking->id]) }}" class="btn btn-primary float-right" style="margin-right: 5px;"><i class="fas fa-download"></i> Generate PDF</a> --}}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('js')



@stop
