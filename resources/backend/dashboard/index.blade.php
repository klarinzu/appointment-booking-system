@extends('adminlte::page')

@php
    $user = auth()->user();
    $appointmentCapacity = config('documate.appointments');
    $nextAction = $user->isStudentUser()
        ? 'Browse your eligible VPSD transactions, wait for approval, then print the official form and book your appointment.'
        : ($user->isStudentOfficer()
            ? 'Review clearance tags, guide students through appointment readiness, and help move requests into review.'
            : 'Approve new requests first, then monitor appointments and transaction progress across the system.');
@endphp

@section('title', 'DOCUMATE Dashboard')

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">DOCUMATE Homepage</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <h1 class="mb-2">Welcome, {{ auth()->user()->name }}.</h1>
                    <p class="doc-note mb-3">Role: {{ $dashboardData['role_label'] }}</p>
                    <p class="mb-0" style="max-width: 780px;">{{ $nextAction }}</p>
                </div>
                <div class="doc-hero-actions">
                    @if ($user->isStudentUser())
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Browse Transactions</a>
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-primary">View Form Examples</a>
                    @elseif ($user->isStudentOfficer())
                        <a href="{{ route('documate.clearances.index') }}" class="btn btn-primary">Manage Clearances</a>
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-primary">Review Transactions</a>
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-secondary">View Form Examples</a>
                    @else
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Review Transactions</a>
                        <a href="{{ route('documate.clearances.index') }}" class="btn btn-outline-primary">Open Clearances</a>
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-secondary">View Form Examples</a>
                    @endif
                    <a href="{{ route('documate.calendar.index') }}" class="btn btn-outline-secondary">Scheduled Calendar</a>
                    <a href="{{ route('documate.handbook.index') }}" class="btn btn-outline-secondary">Student Handbook</a>
                </div>
            </div>

            <div class="doc-inline-stats">
                <div class="doc-inline-stat">
                    <strong>{{ $notifications->count() }}</strong>
                    <span class="doc-note">latest updates waiting for review</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ data_get($appointmentCapacity, 'morning', 25) }}/{{ data_get($appointmentCapacity, 'afternoon', 25) }}</strong>
                    <span class="doc-note">morning and afternoon slots per day</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ count($officeLocations) }}</strong>
                    <span class="doc-note">signatory and office references</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body d-flex flex-column flex-lg-row justify-content-between align-items-lg-center" style="gap: 1rem;">
                        <div>
                            <div class="doc-kicker mb-2">Printable Example Forms</div>
                            <div class="doc-note mb-0">
                                Open the DOCUMATE transactions module and use the <strong>View Example Form</strong> button on any transaction type
                                to preview a printable sample before creating or reviewing a real request.
                            </div>
                        </div>
                        <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Open Example Forms</a>
                    </div>
                </div>
            </div>

            @foreach ($dashboardData['cards'] as $card)
                <div class="col-md-6 col-xl-3 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="doc-kicker mb-2">{{ $card['label'] }}</div>
                                    <div class="display-4 mb-2 font-weight-bold">{{ $card['value'] }}</div>
                                    <div class="doc-note small">
                                        @if ($card['label'] === 'Available Transactions')
                                            Forms you can request right now.
                                        @elseif ($card['label'] === 'Pending Approval')
                                            Requests currently waiting for the next action.
                                        @elseif ($card['label'] === 'Scheduled Appointments' || $card['label'] === 'Appointments Booked')
                                            Confirmed office visits already on the calendar.
                                        @elseif ($card['label'] === 'Completed Requests' || $card['label'] === 'Completed This Month')
                                            Transactions already routed to completion.
                                        @else
                                            Records recently updated inside DOCUMATE.
                                        @endif
                                    </div>
                                </div>
                                <div class="doc-inline-stat p-3 d-inline-flex align-items-center justify-content-center" style="min-width: 72px; min-height: 72px;">
                                    <i class="{{ $card['icon'] }} fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <div class="doc-table-tools">
                            <div>
                                <h3 class="card-title mb-1">Recent Transactions</h3>
                                <div class="doc-note small">Open a record to review its form, status timeline, and appointment details.</div>
                            </div>
                            <a href="{{ route('documate.transactions.index') }}" class="btn btn-sm btn-outline-primary">Open Module</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if ($recentTransactions->isEmpty())
                            <div class="doc-empty-state">
                                No DOCUMATE transactions yet.
                            </div>
                        @else
                            <div class="table-responsive doc-table-stack">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Student</th>
                                            <th>Transaction</th>
                                            <th>Status</th>
                                            <th>Appointment</th>
                                            <th>Requested</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentTransactions as $transaction)
                                            <tr>
                                                <td class="font-weight-bold">{{ $transaction->reference_no }}</td>
                                                <td>
                                                    <div class="doc-record-meta">
                                                        <strong>{{ $transaction->user?->name ?? auth()->user()->name }}</strong>
                                                        <span class="doc-note small">{{ $transaction->user?->studentProfile?->course ?: 'No course listed' }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $transaction->transactionType?->short_name ?? $transaction->transactionType?->name }}</td>
                                                <td>@include('backend.partials.documate-status-badge', ['status' => $transaction->status])</td>
                                                <td>{{ $transaction->appointmentLabel() ?: 'Not scheduled' }}</td>
                                                <td>{{ optional($transaction->requested_at ?? $transaction->created_at)->format('M d, Y h:i A') }}</td>
                                                <td>
                                                    <div class="d-flex flex-wrap justify-content-end" style="gap: 0.5rem;">
                                                        @if ($transaction->transactionType)
                                                            <a href="{{ route('documate.transactions.example', $transaction->transactionType) }}" class="btn btn-sm btn-outline-secondary">Example</a>
                                                        @endif
                                                        <a href="{{ route('documate.transactions.show', $transaction) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="doc-mobile-cards p-3">
                                @foreach ($recentTransactions as $transaction)
                                    <div class="doc-mobile-card doc-surface mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="doc-kicker">{{ $transaction->reference_no }}</div>
                                                <h3 class="h5 mb-1">{{ $transaction->transactionType?->short_name ?? $transaction->transactionType?->name }}</h3>
                                                <div class="doc-note small">{{ $transaction->user?->name ?? auth()->user()->name }}</div>
                                            </div>
                                            @include('backend.partials.documate-status-badge', ['status' => $transaction->status])
                                        </div>
                                        <div class="doc-detail-stack mb-3">
                                            <div class="doc-detail-row">
                                                <span class="doc-label">Appointment</span>
                                                <span>{{ $transaction->appointmentLabel() ?: 'Not scheduled' }}</span>
                                            </div>
                                            <div class="doc-detail-row">
                                                <span class="doc-label">Requested</span>
                                                <span>{{ optional($transaction->requested_at ?? $transaction->created_at)->format('M d, Y h:i A') }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-column flex-sm-row" style="gap: 0.5rem;">
                                            @if ($transaction->transactionType)
                                                <a href="{{ route('documate.transactions.example', $transaction->transactionType) }}" class="btn btn-outline-secondary btn-sm">Example Form</a>
                                            @endif
                                            <a href="{{ route('documate.transactions.show', $transaction) }}" class="btn btn-outline-primary btn-sm">View Record</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">What To Do Next</h3>
                    </div>
                    <div class="card-body">
                        <div class="doc-status-panel mb-3">
                            <div class="doc-kicker mb-2">Recommended action</div>
                            <div>{{ $nextAction }}</div>
                        </div>
                        <ul class="doc-helper-list">
                            @if ($user->isStudentUser())
                                <li>Check your latest request first so you know whether to wait for approval, gather signatures, or attend an appointment.</li>
                                <li>Keep your student profile complete because DOCUMATE reuses it on official forms.</li>
                                <li>Use the handbook if you need office or process guidance before visiting VPSD.</li>
                            @else
                                <li>Prioritize requests waiting for admin approval or appointment verification.</li>
                                <li>Use clearance tags to unblock eligible students or flag those on hold.</li>
                                <li>Guide students toward their correct signatory offices before review or completion.</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Notifications</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($notifications as $notification)
                            <div class="doc-timeline-item mb-3">
                                <div class="font-weight-bold">{{ $notification->data['title'] ?? 'Update' }}</div>
                                <div class="doc-note small mb-2">{{ $notification->data['message'] ?? '' }}</div>
                                <div class="doc-note small">{{ optional($notification->created_at)->diffForHumans() }}</div>
                                @if (!empty($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="btn btn-sm btn-outline-secondary mt-3">Open update</a>
                                @endif
                            </div>
                        @empty
                            <div class="doc-empty-state px-0 py-3 text-left">
                                No notifications yet.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Office Guidance</h3>
                    </div>
                    <div class="card-body">
                        <div class="doc-detail-stack">
                            @foreach ($officeLocations as $office)
                                <div class="doc-directory-item">
                                    <div class="font-weight-bold mb-1">{{ $office['name'] }}</div>
                                    <div class="doc-note small mb-0">{{ $office['description'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                @include('backend.partials.documate-chat')
            </div>
        </div>
    </div>
@stop
