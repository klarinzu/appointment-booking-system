@extends('adminlte::page')

@php
    $appointmentCapacity = config('documate.appointments');
    $isStudentMode = $mode === 'student';
    $pageTitle = $isStudentMode ? 'My Transactions' : 'Transaction Records';
    $eligibleCount = $isStudentMode ? $transactionTypes->where('eligibility.eligible', true)->count() : null;
    $needsActionCount = $isStudentMode ? $transactionTypes->where('eligibility.eligible', false)->count() : null;
    $scheduledCount = $transactions->where('status', 'appointment_scheduled')->count();
    $pendingCount = $transactions->where('status', 'pending_admin_approval')->count();
@endphp

@section('title', $pageTitle)

@section('css')
    <style>
        .doc-record-filter-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 1rem;
        }

        .doc-record-filter-grid .doc-filter-span-3 {
            grid-column: span 3;
        }

        .doc-record-filter-grid .doc-filter-span-2 {
            grid-column: span 2;
        }

        .doc-record-filter-grid .doc-filter-span-1 {
            grid-column: span 1;
        }

        .doc-records-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.75fr);
            gap: 1rem;
            padding: 1.5rem;
            border-bottom: 1px solid var(--doc-border);
            background: linear-gradient(180deg, rgba(7, 3, 114, 0.03), rgba(255, 255, 255, 0.92));
        }

        .doc-record-list {
            display: grid;
            gap: 1rem;
            padding: 1.25rem;
        }

        .doc-transaction-record {
            border: 1px solid var(--doc-border);
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(255, 170, 0, 0.12), transparent 18%),
                linear-gradient(180deg, #ffffff 0%, #fbfbfe 100%);
            box-shadow: 0 16px 36px rgba(7, 3, 114, 0.07);
            padding: 1.25rem;
        }

        .doc-record-top,
        .doc-record-footer,
        .doc-record-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-start;
        }

        .doc-records-toolbar > *,
        .doc-record-top > *,
        .doc-record-footer > *,
        .doc-record-grid > * {
            min-width: 0;
        }

        .doc-record-reference {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.42rem 0.72rem;
            border-radius: 999px;
            background: rgba(7, 3, 114, 0.08);
            color: var(--doc-primary-dark);
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.65rem;
        }

        .doc-record-title h3 {
            margin: 0 0 0.25rem;
            font-size: 1.2rem;
            line-height: 1.28;
            overflow-wrap: anywhere;
        }

        .doc-record-status {
            min-width: min(100%, 240px);
            display: grid;
            gap: 0.55rem;
            justify-items: end;
            text-align: right;
        }

        .doc-record-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 0.9rem;
            margin-top: 1rem;
        }

        .doc-record-cell {
            border: 1px solid rgba(7, 3, 114, 0.09);
            border-radius: 18px;
            padding: 0.95rem 1rem;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 10px 22px rgba(10, 23, 125, 0.05);
        }

        .doc-record-cell strong {
            display: block;
            margin-bottom: 0.2rem;
            font-size: 0.98rem;
        }

        .doc-record-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(7, 3, 114, 0.08);
            align-items: center;
        }

        .doc-record-actions {
            justify-content: flex-end;
            align-items: center;
        }

        .doc-record-actions .btn {
            flex: 1 1 180px;
        }

        .doc-record-search-empty {
            margin: 0 1.25rem 1.25rem;
        }

        @media (max-width: 1199.98px) {
            .doc-record-filter-grid .doc-filter-span-3,
            .doc-record-filter-grid .doc-filter-span-2 {
                grid-column: span 4;
            }

            .doc-record-filter-grid .doc-filter-span-1 {
                grid-column: span 2;
            }

            .doc-record-grid {
                grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .doc-records-toolbar {
                grid-template-columns: 1fr;
            }

            .doc-record-filter-grid .doc-filter-span-3,
            .doc-record-filter-grid .doc-filter-span-2,
            .doc-record-filter-grid .doc-filter-span-1 {
                grid-column: span 6;
            }

            .doc-record-status {
                min-width: 0;
                justify-items: start;
                text-align: left;
            }
        }

        @media (max-width: 767.98px) {
            .doc-record-filter-grid .doc-filter-span-3,
            .doc-record-filter-grid .doc-filter-span-2,
            .doc-record-filter-grid .doc-filter-span-1 {
                grid-column: 1 / -1;
            }

            .doc-record-list,
            .doc-record-search-empty {
                padding-left: 1rem;
                padding-right: 1rem;
                margin-left: 0;
                margin-right: 0;
            }

            .doc-record-grid {
                grid-template-columns: 1fr;
            }

            .doc-record-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .doc-record-actions .btn {
                width: 100%;
                flex-basis: 100%;
            }
        }
    </style>
@stop

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">{{ $isStudentMode ? 'Student Transaction Center' : 'Reviewer Transaction Center' }}</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <h1 class="mb-2">{{ $pageTitle }}</h1>
                    <p class="mb-0" style="max-width: 780px;">
                        {{ $isStudentMode ? 'Choose an eligible VPSD form, request access, track the approval stages, and book an appointment once your documents are ready.' : 'Review requests, apply filters, confirm readiness, and keep the transaction pipeline moving from approval to completion.' }}
                    </p>
                </div>
                <div class="doc-hero-actions">
                    <a href="{{ route('documate.transactions.export', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-outline-secondary">Export CSV</a>
                    <a href="{{ route('documate.transactions.export', array_merge(request()->query(), ['format' => 'json'])) }}" class="btn btn-outline-secondary">Export JSON</a>
                    <a href="{{ route('documate.handbook.index') }}" class="btn btn-primary">Open Handbook</a>
                </div>
            </div>

            <div class="doc-inline-stats">
                @if ($isStudentMode)
                    <div class="doc-inline-stat">
                        <strong>{{ $eligibleCount }}</strong>
                        <span class="doc-note">forms available to request now</span>
                    </div>
                    <div class="doc-inline-stat">
                        <strong>{{ $needsActionCount }}</strong>
                        <span class="doc-note">forms that still need clearance or profile fixes</span>
                    </div>
                @else
                    <div class="doc-inline-stat">
                        <strong>{{ $transactions->count() }}</strong>
                        <span class="doc-note">records matching the current filters</span>
                    </div>
                    <div class="doc-inline-stat">
                        <strong>{{ $pendingCount }}</strong>
                        <span class="doc-note">requests waiting for approval</span>
                    </div>
                @endif
                <div class="doc-inline-stat">
                    <strong>{{ $scheduledCount }}</strong>
                    <span class="doc-note">transactions with booked appointments</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ data_get($appointmentCapacity, 'morning', 25) }}/{{ data_get($appointmentCapacity, 'afternoon', 25) }}</strong>
                    <span class="doc-note">morning and afternoon slots per day</span>
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

    @if ($isStudentMode)
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 mb-3 mb-lg-0">
                        <div class="doc-search-bar">
                            <label for="transaction-catalog-search" class="doc-label mb-2 d-block">Find a form faster</label>
                            <input type="text" id="transaction-catalog-search" class="form-control border-0" placeholder="Search by form code, title, signatory, or keyword">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="doc-filter-summary">
                            <div class="doc-kicker mb-2">Appointment policy</div>
                            <div class="doc-note">
                                Each day accepts {{ data_get($appointmentCapacity, 'daily', 50) }} students total:
                                {{ data_get($appointmentCapacity, 'morning', 25) }} in the morning and
                                {{ data_get($appointmentCapacity, 'afternoon', 25) }} in the afternoon.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="doc-chip-row mt-3">
                    <span class="doc-chip">Eligible forms: {{ $eligibleCount }}</span>
                    <span class="doc-chip">Needs action: {{ $needsActionCount }}</span>
                    <span class="doc-chip">History records: {{ $transactions->count() }}</span>
                </div>
            </div>
        </div>

        <div class="row" id="transaction-catalog">
            @foreach ($transactionTypes as $type)
                <div class="col-xl-6 mb-4 transaction-catalog-item" data-search="{{ strtolower(implode(' ', array_filter([
                    $type->code,
                    $type->name,
                    $type->description,
                    collect($type->required_signatories ?? [])->implode(' '),
                    collect($type->workflow_steps ?? [])->implode(' '),
                ]))) }}">
                    <div class="card h-100 {{ $type->eligibility['eligible'] ? '' : 'border-warning' }}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start mb-3">
                                <div class="pr-md-3 mb-3 mb-md-0">
                                    <div class="doc-kicker mb-2">{{ $type->code }}</div>
                                    <h3 class="h4 mb-2">{{ $type->name }}</h3>
                                    <p class="doc-note mb-0">{{ $type->description }}</p>
                                </div>
                                <div>
                                    <span class="doc-status-badge {{ $type->eligibility['eligible'] ? 'doc-status-success' : 'doc-status-warning' }}">
                                        {{ $type->eligibility['eligible'] ? 'Eligible to Request' : 'Needs Action First' }}
                                    </span>
                                </div>
                            </div>

                            <div class="doc-chip-row mb-3">
                                <span class="doc-chip">{{ count($type->workflow_steps ?? []) }} workflow steps</span>
                                <span class="doc-chip">{{ count($type->required_signatories ?? []) ?: 0 }} signatories</span>
                                <span class="doc-chip">{{ $type->requires_notary ? 'Needs notary' : 'No notary needed' }}</span>
                            </div>

                            <div class="doc-detail-stack mb-3">
                                <div class="doc-detail-row">
                                    <span class="doc-label">Required signatories</span>
                                    <span>{{ collect($type->required_signatories)->implode(', ') ?: 'None listed' }}</span>
                                </div>
                            </div>

                            <details class="doc-surface mb-3">
                                <summary class="font-weight-bold" style="cursor: pointer;">View instructions and workflow</summary>
                                <ol class="doc-workflow-list mt-3 mb-0">
                                    @foreach ($type->workflow_steps as $step)
                                        <li>{{ $step }}</li>
                                    @endforeach
                                </ol>
                            </details>

                            @if (!$type->eligibility['eligible'])
                                <div class="alert alert-warning">
                                    <strong>Why this form is not available yet</strong>
                                    <div class="mt-2">{{ $type->eligibility['reasons'][0] }}</div>
                                    @if (count($type->eligibility['reasons']) > 1)
                                        <div class="small mt-2">
                                            {{ collect($type->eligibility['reasons'])->slice(1)->implode(' ') }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="doc-filter-summary mb-3">
                                    <div class="doc-kicker mb-2">Before requesting</div>
                                    <div class="doc-note">Make sure your personal and academic profile details are accurate so the official form can be auto-filled correctly.</div>
                                </div>
                            @endif

                            <form action="{{ route('documate.transactions.store') }}" method="POST" class="mt-auto">
                                @csrf
                                <input type="hidden" name="transaction_type_id" value="{{ $type->id }}">
                                <div class="form-group">
                                    <label for="note-{{ $type->id }}">Optional note for the approving office</label>
                                    <textarea class="form-control" id="note-{{ $type->id }}" name="student_notes" rows="2" placeholder="Add context if this request needs special attention"></textarea>
                                </div>
                                <div class="d-flex flex-column flex-sm-row" style="gap: 0.75rem;">
                                    <button type="submit" class="btn btn-primary flex-fill" {{ $type->eligibility['eligible'] ? '' : 'disabled' }}>
                                        Request Form Access
                                    </button>
                                    <a href="{{ route('documate.transactions.example', $type) }}" class="btn btn-outline-secondary flex-fill">
                                        View Example Form
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="transaction-catalog-empty" class="card d-none mb-4">
            <div class="card-body doc-empty-state">
                No transaction form matched your search. Try a form code like `F-SDM-004`, a title keyword, or a signatory office.
            </div>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET">
                    <div class="doc-record-filter-grid">
                        <div class="form-group doc-filter-span-3 mb-0">
                            <label for="filter-transaction">Transaction</label>
                            <select id="filter-transaction" name="transaction_type_id" class="form-control">
                                <option value="">All transaction types</option>
                                @foreach ($transactionTypes as $type)
                                    <option value="{{ $type->id }}" @selected(request('transaction_type_id') == $type->id)>{{ $type->code }} - {{ $type->short_name ?? $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group doc-filter-span-2 mb-0">
                            <label for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-control">
                                <option value="">All statuses</option>
                                @foreach (config('documate.statuses') as $statusKey => $statusLabel)
                                    <option value="{{ $statusKey }}" @selected(request('status') === $statusKey)>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group doc-filter-span-2 mb-0">
                            <label for="filter-month">Month</label>
                            <input id="filter-month" type="month" class="form-control" name="month" value="{{ request('month') }}">
                        </div>
                        <div class="form-group doc-filter-span-2 mb-0">
                            <label for="filter-course">Course</label>
                            <input id="filter-course" type="text" class="form-control" name="course" value="{{ request('course') }}" placeholder="Filter by course">
                        </div>
                        <div class="form-group doc-filter-span-1 mb-0">
                            <label for="filter-sort">Sort</label>
                            <select id="filter-sort" name="sort_by" class="form-control">
                                <option value="requested_at" @selected(request('sort_by', 'requested_at') === 'requested_at')>Date</option>
                                <option value="transaction" @selected(request('sort_by') === 'transaction')>Transaction</option>
                                <option value="course" @selected(request('sort_by') === 'course')>Course</option>
                                <option value="month" @selected(request('sort_by') === 'month')>Month</option>
                                <option value="appointment" @selected(request('sort_by') === 'appointment')>Appointment</option>
                                <option value="status" @selected(request('sort_by') === 'status')>Status</option>
                            </select>
                        </div>
                        <div class="form-group doc-filter-span-1 mb-0">
                            <label for="filter-direction">Order</label>
                            <select id="filter-direction" name="direction" class="form-control">
                                <option value="desc" @selected(request('direction', 'desc') === 'desc')>Newest</option>
                                <option value="asc" @selected(request('direction') === 'asc')>Oldest</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mt-4" style="gap: 1rem;">
                        <div class="doc-chip-row">
                            <span class="doc-chip">Showing {{ $transactions->count() }} record{{ $transactions->count() === 1 ? '' : 's' }}</span>
                            @if (request()->filled('status'))
                                <span class="doc-chip">Status: {{ config('documate.statuses.' . request('status')) ?? request('status') }}</span>
                            @endif
                            @if (request()->filled('course'))
                                <span class="doc-chip">Course: {{ request('course') }}</span>
                            @endif
                            @if (request()->filled('month'))
                                <span class="doc-chip">Month: {{ request('month') }}</span>
                            @endif
                        </div>

                        <div class="doc-toolbar">
                            @if (request()->query())
                                <a href="{{ route('documate.transactions.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                            @endif
                            <button class="btn btn-primary" type="submit">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="doc-table-tools">
                <div>
                    <h3 class="card-title mb-1">{{ $isStudentMode ? 'My Transaction History' : 'Transaction Records' }}</h3>
                    <div class="doc-note small">
                        {{ $isStudentMode ? 'Use this history to reopen your requests, check progress, and confirm your appointment details.' : 'Use this workspace to review official forms, status transitions, and appointment schedules without digging through a dense table.' }}
                    </div>
                </div>
                @if ($transactions->isNotEmpty())
                    <span class="doc-chip">Total records: {{ $transactions->count() }}</span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if ($transactions->isEmpty())
                <div class="doc-empty-state">
                    No transaction records found.
                </div>
            @else
                <div class="doc-records-toolbar">
                    <div class="doc-search-bar">
                        <label for="transaction-record-search" class="doc-label mb-2 d-block">Search current records</label>
                        <input type="text" id="transaction-record-search" class="form-control border-0" placeholder="Search by reference, student, transaction, course, appointment, or status">
                    </div>
                    <div class="doc-filter-summary">
                        <div class="doc-kicker mb-2">How to read this list</div>
                        <div class="doc-note">
                            {{ $isStudentMode ? 'Each record shows your current stage, appointment details, and request date first so you can quickly tell which request needs attention.' : 'Each record shows the student, academic details, appointment state, and latest stage up front so reviewers can decide whether to open or act immediately.' }}
                        </div>
                    </div>
                </div>

                <div class="doc-record-list" id="transaction-record-list">
                    @foreach ($transactions as $transaction)
                        @php
                            $studentName = $transaction->user?->name ?? auth()->user()->name;
                            $studentEmail = $transaction->user?->email ?: 'No email listed';
                            $studentNumber = $transaction->user?->studentProfile?->student_number ?: 'Not set';
                            $course = $transaction->user?->studentProfile?->course ?: 'Not set';
                            $appointmentLabel = $transaction->appointmentLabel() ?: 'Not scheduled';
                            $requestedAt = optional($transaction->requested_at ?? $transaction->created_at);
                            $recordHint = match ($transaction->status) {
                                'pending_admin_approval' => 'Waiting for administrator approval before the official form becomes available.',
                                'approved_for_form_access' => 'Official form is unlocked and ready for printing or routing.',
                                'for_signatory' => 'Printed form is being routed for the required signatures.',
                                'for_notary' => 'Form is waiting for notarization before office submission.',
                                'appointment_scheduled' => 'Appointment is booked and ready for in-person submission.',
                                'under_review' => 'Office is reviewing the submitted transaction package.',
                                'completed' => 'Transaction is already completed and retained for audit or reprint.',
                                'rejected' => 'Transaction was rejected and needs a corrected follow-up action.',
                                default => 'Open the record to review the full transaction context.',
                            };
                            $recordSearch = strtolower(implode(' ', array_filter([
                                $transaction->reference_no,
                                $studentName,
                                $studentEmail,
                                $studentNumber,
                                $course,
                                $transaction->transactionType?->code,
                                $transaction->transactionType?->name,
                                $transaction->transactionType?->short_name,
                                config('documate.statuses.' . $transaction->status),
                                $appointmentLabel,
                                $requestedAt?->format('M d Y h:i A'),
                            ])));
                            $canOpenForm = !in_array($transaction->status, ['pending_admin_approval', 'rejected'], true);
                        @endphp
                        <article class="doc-transaction-record" data-record-search="{{ $recordSearch }}">
                            <div class="doc-record-top">
                                <div class="doc-record-title pr-lg-3">
                                    <div class="doc-record-reference">{{ $transaction->reference_no }}</div>
                                    <h3>{{ $transaction->transactionType?->short_name ?? $transaction->transactionType?->name }}</h3>
                                    <div class="doc-note small">
                                        {{ $transaction->transactionType?->code }}
                                        @if (!$isStudentMode)
                                            <span class="mx-1">|</span>
                                            {{ $studentName }}
                                        @endif
                                    </div>
                                </div>
                                <div class="doc-record-status">
                                    @include('backend.partials.documate-status-badge', ['status' => $transaction->status])
                                    <div class="doc-note small">{{ $recordHint }}</div>
                                </div>
                            </div>

                            <div class="doc-record-grid">
                                <div class="doc-record-cell">
                                    <span class="doc-label">{{ $isStudentMode ? 'Record owner' : 'Student' }}</span>
                                    <strong>{{ $studentName }}</strong>
                                    <div class="doc-note small">{{ $studentEmail }}</div>
                                </div>
                                <div class="doc-record-cell">
                                    <span class="doc-label">Academic details</span>
                                    <strong>{{ $course }}</strong>
                                    <div class="doc-note small">Student no. {{ $studentNumber }}</div>
                                </div>
                                <div class="doc-record-cell">
                                    <span class="doc-label">Appointment</span>
                                    <strong>{{ $appointmentLabel }}</strong>
                                    <div class="doc-note small">{{ $transaction->appointment_date ? 'Reserved visit slot' : 'Still waiting for schedule' }}</div>
                                </div>
                                <div class="doc-record-cell">
                                    <span class="doc-label">Requested</span>
                                    <strong>{{ $requestedAt?->format('M d, Y') ?: 'Not available' }}</strong>
                                    <div class="doc-note small">{{ $requestedAt?->format('h:i A') ?: 'No timestamp' }}</div>
                                </div>
                            </div>

                            <div class="doc-record-footer">
                                <div class="doc-chip-row">
                                    <span class="doc-chip">{{ $transaction->transactionType?->code }}</span>
                                    <span class="doc-chip">{{ count($transaction->transactionType?->required_signatories ?? []) }} signator{{ count($transaction->transactionType?->required_signatories ?? []) === 1 ? 'y' : 'ies' }}</span>
                                    <span class="doc-chip">{{ $transaction->transactionType?->requires_notary ? 'Needs notary' : 'No notary' }}</span>
                                </div>

                                <div class="doc-record-actions">
                                    @if ($transaction->transactionType)
                                        <a href="{{ route('documate.transactions.example', $transaction->transactionType) }}" class="btn btn-sm btn-outline-secondary">Example Form</a>
                                    @endif
                                    @if ($canOpenForm)
                                        <a href="{{ route('documate.transactions.form', $transaction) }}" class="btn btn-sm btn-outline-secondary">Official Form</a>
                                    @endif
                                    <a href="{{ route('documate.transactions.show', $transaction) }}" class="btn btn-sm btn-primary">Open Record</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div id="transaction-record-empty" class="card d-none doc-record-search-empty">
                    <div class="card-body doc-empty-state">
                        No transaction record matched your search. Try a reference number, student name, form title, course, or status.
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recordSearchInput = document.getElementById('transaction-record-search');
            const recordItems = Array.from(document.querySelectorAll('.doc-transaction-record'));
            const recordEmptyState = document.getElementById('transaction-record-empty');

            if (recordSearchInput && recordItems.length && recordEmptyState) {
                const filterRecords = function() {
                    const query = recordSearchInput.value.trim().toLowerCase();
                    let visible = 0;

                    recordItems.forEach(function(item) {
                        const haystack = item.dataset.recordSearch || '';
                        const matches = !query || haystack.includes(query);
                        item.classList.toggle('d-none', !matches);

                        if (matches) {
                            visible += 1;
                        }
                    });

                    recordEmptyState.classList.toggle('d-none', visible > 0);
                };

                recordSearchInput.addEventListener('input', filterRecords);
            }

            @if ($isStudentMode)
                const catalogSearchInput = document.getElementById('transaction-catalog-search');
                const catalogItems = Array.from(document.querySelectorAll('.transaction-catalog-item'));
                const catalogEmptyState = document.getElementById('transaction-catalog-empty');

                if (catalogSearchInput && catalogItems.length && catalogEmptyState) {
                    const filterCatalog = function() {
                        const query = catalogSearchInput.value.trim().toLowerCase();
                        let visible = 0;

                        catalogItems.forEach(function(item) {
                            const haystack = item.dataset.search || '';
                            const matches = !query || haystack.includes(query);
                            item.classList.toggle('d-none', !matches);

                            if (matches) {
                                visible += 1;
                            }
                        });

                        catalogEmptyState.classList.toggle('d-none', visible > 0);
                    };

                    catalogSearchInput.addEventListener('input', filterCatalog);
                }
            @endif
        });
    </script>
@endpush
