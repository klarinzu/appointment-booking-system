@extends('adminlte::page')

@php
    $user = auth()->user();
    $isOwner = $user->id === $transaction->user_id;
    $isAdmin = $user->isDocumateAdmin();
    $isOfficer = $user->isStudentOfficer();
    $canReview = ($isAdmin && !in_array($transaction->status, ['completed', 'rejected'], true))
        || ($isOfficer && !in_array($transaction->status, ['pending_admin_approval', 'completed', 'rejected'], true));
    $canStudentUpdate = $isOwner && !in_array($transaction->status, ['pending_admin_approval', 'rejected', 'completed', 'appointment_scheduled'], true);
    $canSchedule = $isOwner && in_array($transaction->status, ['approved_for_form_access', 'for_signatory', 'for_notary', 'appointment_scheduled'], true);
    $availableDate = $appointmentAvailability->first(fn ($slot) => $slot['daily_remaining'] > 0);
    $selectedDate = optional($transaction->appointment_date)->toDateString() ?: data_get($availableDate, 'date_key', now()->toDateString());
    $selectedAvailability = $appointmentAvailability->firstWhere('date_key', $selectedDate) ?: $availableDate;
    $nextStep = match ($transaction->status) {
        'pending_admin_approval' => $isAdmin
            ? 'Review the request and decide whether to unlock the official form for the student.'
            : 'Wait for administrator approval before the official form becomes available.',
        'approved_for_form_access' => $isOwner
            ? 'Open the official form, print it, and begin collecting the required signatures.'
            : 'Confirm that the student starts manual routing and prepares for appointment booking.',
        'for_signatory' => 'The form is currently being routed for manual signatures. Keep the student focused on the listed signatories.',
        'for_notary' => 'The form needs notarization before the student proceeds to the final appointment.',
        'appointment_scheduled' => 'The appointment is already booked. The next step is to attend the confirmed schedule and complete office review.',
        'under_review' => 'The request is already under office review. Check remarks and finalize the transaction when ready.',
        'completed' => 'This transaction is already completed. The record is now mainly for audit and printing.',
        'rejected' => 'This request has been rejected. Review the remarks and advise the student on the next valid action.',
        default => 'Review the current timeline, notes, and appointment readiness before making changes.',
    };
@endphp

@section('title', 'Transaction Detail')

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">{{ $transaction->transactionType?->code ?? 'DOCUMATE Record' }}</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <div class="d-flex flex-wrap align-items-center mb-3" style="gap: 0.75rem;">
                        <h1 class="mb-0">{{ $transaction->transactionType?->name }}</h1>
                        @include('backend.partials.documate-status-badge', ['status' => $transaction->status, 'label' => $statusLabels[$transaction->status] ?? $transaction->status])
                    </div>
                    <p class="doc-note mb-2">Reference: {{ $transaction->reference_no }}</p>
                    <p class="mb-0" style="max-width: 760px;">{{ $nextStep }}</p>
                </div>
                <div class="doc-hero-actions">
                    <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-secondary">Back to Records</a>
                    @if ($transaction->transactionType)
                        <a href="{{ route('documate.transactions.example', $transaction->transactionType) }}" class="btn btn-outline-secondary">Example Form</a>
                    @endif
                    @if (!in_array($transaction->status, ['pending_admin_approval', 'rejected'], true))
                        <a href="{{ route('documate.transactions.form', $transaction) }}" class="btn btn-primary">View Official Form</a>
                        <a href="{{ route('documate.transactions.download', $transaction) }}" class="btn btn-outline-primary">Download Form</a>
                    @endif
                </div>
            </div>

            <div class="doc-inline-stats">
                <div class="doc-inline-stat">
                    <strong>{{ optional($transaction->requested_at ?? $transaction->created_at)->format('M d') }}</strong>
                    <span class="doc-note">request date</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ $transaction->appointmentLabel() ?: 'Pending' }}</strong>
                    <span class="doc-note">appointment schedule</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ count($transaction->transactionType?->required_signatories ?? []) }}</strong>
                    <span class="doc-note">required signatories</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ $appointmentCapacity['daily'] }}</strong>
                    <span class="doc-note">students allowed each day</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Transaction Summary</h3>
                </div>
                <div class="card-body">
                    <div class="doc-info-grid mb-4">
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Student</div>
                            <div class="font-weight-bold">{{ $transaction->user?->name }}</div>
                            <div class="doc-note small">{{ $transaction->user?->studentProfile?->student_number ?: 'No student number' }}</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Requested</div>
                            <div class="font-weight-bold">{{ optional($transaction->requested_at ?? $transaction->created_at)->format('M d, Y h:i A') }}</div>
                            <div class="doc-note small">Transaction request submission time</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Completed</div>
                            <div class="font-weight-bold">{{ optional($transaction->completed_at)->format('M d, Y h:i A') ?: 'Pending' }}</div>
                            <div class="doc-note small">Final routing timestamp</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Capacity Rule</div>
                            <div class="font-weight-bold">{{ $appointmentCapacity['morning'] }} AM / {{ $appointmentCapacity['afternoon'] }} PM</div>
                            <div class="doc-note small">{{ $appointmentCapacity['daily'] }} students total each day</div>
                        </div>
                    </div>

                    <div class="doc-card-grid">
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Student Notes</div>
                            <div>{{ $transaction->student_notes ?: 'No student notes provided.' }}</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Admin Notes</div>
                            <div>{{ $transaction->admin_notes ?: 'No admin notes yet.' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Instructions and Workflow</h3>
                </div>
                <div class="card-body">
                    <div class="doc-chip-row mb-4">
                        @forelse (($transaction->transactionType?->required_signatories ?? []) as $signatory)
                            <span class="doc-chip">{{ $signatory }}</span>
                        @empty
                            <span class="doc-chip">No signatories listed</span>
                        @endforelse
                        <span class="doc-chip">{{ $transaction->transactionType?->requires_notary ? 'Requires notarization' : 'No notarization required' }}</span>
                    </div>

                    <ol class="doc-workflow-list mb-0">
                        @foreach ($transaction->transactionType?->workflow_steps ?? [] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Status Timeline</h3>
                </div>
                <div class="card-body">
                    @if ($transaction->updates->isEmpty())
                        <div class="doc-empty-state px-0 py-3 text-left">No status updates yet.</div>
                    @else
                        <div class="doc-timeline">
                            @foreach ($transaction->updates as $update)
                                <div class="doc-timeline-item">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-2">
                                        <div class="mb-2 mb-md-0">
                                            @include('backend.partials.documate-status-badge', ['status' => $update->to_status, 'label' => $statusLabels[$update->to_status] ?? $update->to_status])
                                        </div>
                                        <div class="doc-note small">
                                            {{ optional($update->created_at)->format('M d, Y h:i A') }}
                                            @if ($update->actor)
                                                by {{ $update->actor->name }}
                                            @endif
                                        </div>
                                    </div>
                                    @if ($update->remarks)
                                        <div>{{ $update->remarks }}</div>
                                    @else
                                        <div class="doc-note">No remarks recorded for this update.</div>
                                    @endif
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
                    <h3 class="card-title mb-0">Recommended Next Step</h3>
                </div>
                <div class="card-body">
                    <div class="doc-status-panel mb-3">
                        <div class="doc-kicker mb-2">Current guidance</div>
                        <div>{{ $nextStep }}</div>
                    </div>

                    <div class="doc-detail-stack">
                        <div class="doc-detail-row">
                            <span class="doc-label">Current appointment</span>
                            <span>{{ $transaction->appointmentLabel() ?: 'Not scheduled yet' }}</span>
                        </div>
                        <div class="doc-detail-row">
                            <span class="doc-label">Next office-ready status</span>
                            <span>{{ $transaction->appointment_date ? 'Under Review / Completed' : 'Book appointment first' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if ($isAdmin && $transaction->status === 'pending_admin_approval')
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Admin Approval</h3>
                    </div>
                    <div class="card-body">
                        <p class="doc-note">Approving this request unlocks the official form and allows the student to continue with manual routing and appointment booking.</p>
                        <form method="POST" action="{{ route('documate.transactions.approve', $transaction) }}">
                            @csrf
                            <div class="form-group">
                                <label for="admin-notes">Approval Notes</label>
                                <textarea id="admin-notes" name="admin_notes" class="form-control" rows="3" placeholder="Optional approval guidance"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Approve Form Access</button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($canStudentUpdate)
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Student Status Update</h3>
                    </div>
                    <div class="card-body">
                        <p class="doc-note">Use this only when you have already moved the printed form to the next manual stage.</p>
                        <form method="POST" action="{{ route('documate.transactions.status.update', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="student-status">Mark Current Stage</label>
                                <select id="student-status" name="status" class="form-control">
                                    <option value="for_signatory">For Signatory</option>
                                    @if ($transaction->transactionType?->requires_notary)
                                        <option value="for_notary">For Notary</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student-remarks">Remarks</label>
                                <textarea id="student-remarks" name="remarks" class="form-control" rows="2" placeholder="Add context if needed"></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-primary w-100">Update My Stage</button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($canSchedule)
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Book Appointment</h3>
                    </div>
                    <div class="card-body">
                        <div class="doc-filter-summary mb-3">
                            <div class="doc-kicker mb-2">Daily slot limit</div>
                            <div class="doc-note">
                                {{ $appointmentCapacity['daily'] }} students per day:
                                {{ $appointmentCapacity['morning'] }} in the morning and
                                {{ $appointmentCapacity['afternoon'] }} in the afternoon.
                            </div>
                        </div>

                        <form method="POST" action="{{ route('documate.transactions.schedule', $transaction) }}">
                            @csrf
                            <div class="form-group">
                                <label for="appointment-date">Appointment Date</label>
                                <input id="appointment-date" type="date" name="appointment_date" class="form-control" min="{{ now()->toDateString() }}" value="{{ $selectedDate }}" required>
                            </div>
                            <div class="form-group">
                                <label for="appointment-session">Session</label>
                                <select id="appointment-session" name="appointment_session" class="form-control">
                                    @foreach ($appointmentCapacity['sessions'] as $sessionKey => $sessionLabel)
                                        <option value="{{ $sessionKey }}" @selected($transaction->appointment_session === $sessionKey)>{{ $sessionLabel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="appointment-remarks">Remarks</label>
                                <textarea id="appointment-remarks" name="remarks" class="form-control" rows="2" placeholder="Optional scheduling remarks"></textarea>
                            </div>
                            <div class="doc-status-panel mb-3" id="appointment-availability-summary">
                                <div class="doc-kicker mb-2">Availability for selected date</div>
                                @if ($selectedAvailability)
                                    <div class="doc-note">
                                        Morning slots left: {{ $selectedAvailability['morning_remaining'] }}.
                                        Afternoon slots left: {{ $selectedAvailability['afternoon_remaining'] }}.
                                        Total slots left: {{ $selectedAvailability['daily_remaining'] }}.
                                    </div>
                                @else
                                    <div class="doc-note">Select a date to view remaining slots.</div>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Appointment</button>
                        </form>

                        @if ($transaction->appointmentLabel())
                            <div class="alert alert-info mt-3 mb-0">
                                Current appointment: {{ $transaction->appointmentLabel() }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Upcoming Availability</h3>
                    </div>
                    <div class="card-body">
                        <div class="doc-mini-grid">
                            @foreach ($appointmentAvailability->take(6) as $slot)
                                <div class="doc-surface">
                                    <div class="doc-kicker mb-2">{{ $slot['date']->format('M d, Y') }}</div>
                                    <div class="doc-note small mb-1">Morning left: {{ $slot['morning_remaining'] }}</div>
                                    <div class="doc-note small mb-1">Afternoon left: {{ $slot['afternoon_remaining'] }}</div>
                                    <div class="font-weight-bold">Total left: {{ $slot['daily_remaining'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($canReview)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title mb-0">Review Status</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('documate.transactions.status.update', $transaction) }}">
                            @csrf
                            @method('PATCH')
                            <div class="form-group">
                                <label for="review-status">Set Status</label>
                                <select id="review-status" name="status" class="form-control">
                                    @if ($transaction->status === 'pending_admin_approval')
                                        @if ($isAdmin)
                                            <option value="approved_for_form_access">Approved for Form Access</option>
                                        @endif
                                        <option value="rejected">Rejected</option>
                                    @else
                                        @if ($isAdmin)
                                            <option value="approved_for_form_access">Approved for Form Access</option>
                                        @endif
                                        <option value="under_review">Under Review</option>
                                        <option value="completed">Completed</option>
                                        <option value="rejected">Rejected</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="review-remarks">Remarks</label>
                                <textarea id="review-remarks" name="remarks" class="form-control" rows="2" placeholder="Record what happened at this stage"></textarea>
                            </div>
                            @if (!$transaction->appointment_date && $transaction->status !== 'pending_admin_approval')
                                <div class="alert alert-warning">
                                    Schedule the student appointment first before moving this request to review or completion.
                                </div>
                            @endif
                            <button type="submit" class="btn btn-danger w-100">Save Review Status</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@push('js')
    @if ($canSchedule)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dateInput = document.getElementById('appointment-date');
                const sessionInput = document.getElementById('appointment-session');
                const summary = document.getElementById('appointment-availability-summary');
                const availability = @json(
                    $appointmentAvailability->mapWithKeys(fn ($slot) => [
                        $slot['date_key'] => [
                            'morning' => $slot['morning_remaining'],
                            'afternoon' => $slot['afternoon_remaining'],
                            'daily' => $slot['daily_remaining'],
                        ],
                    ])
                );

                if (!dateInput || !sessionInput || !summary) {
                    return;
                }

                const renderAvailability = function() {
                    const selected = availability[dateInput.value];

                    if (!selected) {
                        summary.innerHTML = '<div class="doc-kicker mb-2">Availability for selected date</div><div class="doc-note">No availability data is ready for that date yet. Try another date within the upcoming schedule.</div>';
                        return;
                    }

                    const currentSession = sessionInput.value;
                    const sessionLeft = currentSession === 'afternoon' ? selected.afternoon : selected.morning;
                    summary.innerHTML = `
                        <div class="doc-kicker mb-2">Availability for selected date</div>
                        <div class="doc-note">
                            Morning slots left: ${selected.morning}. Afternoon slots left: ${selected.afternoon}. Total slots left: ${selected.daily}.
                        </div>
                        <div class="doc-note small mt-2">
                            Selected session remaining: ${sessionLeft}.
                        </div>
                    `;
                };

                dateInput.addEventListener('change', renderAvailability);
                sessionInput.addEventListener('change', renderAvailability);
                renderAvailability();
            });
        </script>
    @endif
@endpush
