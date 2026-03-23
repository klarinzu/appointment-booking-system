@extends('adminlte::page')

@php
    $pendingProfiles = $profiles->where('clearance_status', 'pending')->count();
    $clearedProfiles = $profiles->where('clearance_status', 'cleared')->count();
    $holdProfiles = $profiles->where('clearance_status', 'hold')->count();
@endphp

@section('title', 'Clearance Tagging')

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">Clearance Management</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <h1 class="mb-2">Clearance Tagging</h1>
                    <p class="mb-0" style="max-width: 760px;">Student officers and administrators can update clearance status here to control transaction eligibility and help students understand what needs attention before requesting a form.</p>
                </div>
                <div class="doc-hero-actions">
                    <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Open Transactions</a>
                </div>
            </div>

            <div class="doc-inline-stats">
                <div class="doc-inline-stat">
                    <strong>{{ $pendingProfiles }}</strong>
                    <span class="doc-note">students still pending clearance review</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ $clearedProfiles }}</strong>
                    <span class="doc-note">students currently cleared</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ $holdProfiles }}</strong>
                    <span class="doc-note">students currently on hold</span>
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

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row">
                <div class="col-md-5 mb-3">
                    <label for="clearance-course">Course</label>
                    <input id="clearance-course" type="text" name="course" class="form-control" value="{{ request('course') }}" placeholder="Filter by course">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="clearance-status">Clearance Status</label>
                    <select id="clearance-status" name="clearance_status" class="form-control">
                        <option value="">All</option>
                        <option value="pending" @selected(request('clearance_status') === 'pending')>Pending</option>
                        <option value="cleared" @selected(request('clearance_status') === 'cleared')>Cleared</option>
                        <option value="hold" @selected(request('clearance_status') === 'hold')>Hold</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100" type="submit">Apply Filters</button>
                </div>
            </form>

            <div class="doc-chip-row">
                <span class="doc-chip">Showing {{ $profiles->count() }} student record{{ $profiles->count() === 1 ? '' : 's' }}</span>
                @if (request('course'))
                    <span class="doc-chip">Course: {{ request('course') }}</span>
                @endif
                @if (request('clearance_status'))
                    <span class="doc-chip">Status: {{ ucfirst(request('clearance_status')) }}</span>
                @endif
                @if (request()->query())
                    <a href="{{ route('documate.clearances.index') }}" class="btn btn-sm btn-outline-secondary">Clear filters</a>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="doc-table-tools">
                <div>
                    <h3 class="card-title mb-1">Student Clearance Records</h3>
                    <div class="doc-note small">Save the correct status and add notes so other DOCUMATE modules can validate transaction eligibility correctly.</div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($profiles->isEmpty())
                <div class="doc-empty-state">
                    No student profiles matched the selected filters.
                </div>
            @else
                <div class="table-responsive doc-table-stack">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Student No.</th>
                                <th>Course</th>
                                <th>Current Status</th>
                                <th>Tagged By</th>
                                <th>Notes</th>
                                <th style="min-width: 320px;">Update</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($profiles as $profile)
                                @php
                                    $clearanceStatusKey = match ($profile->clearance_status) {
                                        'cleared' => 'completed',
                                        'hold' => 'rejected',
                                        default => 'pending_admin_approval',
                                    };
                                @endphp
                                <tr>
                                    <td>
                                        <div class="doc-record-meta">
                                            <strong>{{ $profile->user?->name }}</strong>
                                            <span class="doc-note small">{{ $profile->user?->email }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $profile->student_number }}</td>
                                    <td>{{ $profile->course }}</td>
                                    <td>
                                        @include('backend.partials.documate-status-badge', ['status' => $clearanceStatusKey, 'label' => ucfirst($profile->clearance_status)])
                                        @if ($profile->tagged_at)
                                            <div class="doc-note small mt-2">{{ $profile->tagged_at->format('M d, Y h:i A') }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $profile->taggedBy?->name ?? 'Not tagged yet' }}</td>
                                    <td>{{ $profile->clearance_notes ?: 'No notes' }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('documate.clearances.update', $profile) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="form-group mb-2">
                                                <select name="clearance_status" class="form-control">
                                                    <option value="pending" @selected($profile->clearance_status === 'pending')>Pending</option>
                                                    <option value="cleared" @selected($profile->clearance_status === 'cleared')>Cleared</option>
                                                    <option value="hold" @selected($profile->clearance_status === 'hold')>Hold</option>
                                                </select>
                                            </div>
                                            <div class="form-group mb-2">
                                                <textarea name="clearance_notes" class="form-control" rows="2" placeholder="Add clearance notes">{{ $profile->clearance_notes }}</textarea>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-primary">Save Clearance Tag</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="doc-mobile-cards p-3">
                    @foreach ($profiles as $profile)
                        @php
                            $clearanceStatusKey = match ($profile->clearance_status) {
                                'cleared' => 'completed',
                                'hold' => 'rejected',
                                default => 'pending_admin_approval',
                            };
                        @endphp
                        <div class="doc-mobile-card doc-surface mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="pr-3">
                                    <h3 class="h5 mb-1">{{ $profile->user?->name }}</h3>
                                    <div class="doc-note small">{{ $profile->user?->email }}</div>
                                </div>
                                @include('backend.partials.documate-status-badge', ['status' => $clearanceStatusKey, 'label' => ucfirst($profile->clearance_status)])
                            </div>
                            <div class="doc-detail-stack mb-3">
                                <div class="doc-detail-row">
                                    <span class="doc-label">Student no.</span>
                                    <span>{{ $profile->student_number }}</span>
                                </div>
                                <div class="doc-detail-row">
                                    <span class="doc-label">Course</span>
                                    <span>{{ $profile->course }}</span>
                                </div>
                                <div class="doc-detail-row">
                                    <span class="doc-label">Tagged by</span>
                                    <span>{{ $profile->taggedBy?->name ?? 'Not tagged yet' }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('documate.clearances.update', $profile) }}">
                                @csrf
                                @method('PATCH')
                                <div class="form-group mb-2">
                                    <select name="clearance_status" class="form-control">
                                        <option value="pending" @selected($profile->clearance_status === 'pending')>Pending</option>
                                        <option value="cleared" @selected($profile->clearance_status === 'cleared')>Cleared</option>
                                        <option value="hold" @selected($profile->clearance_status === 'hold')>Hold</option>
                                    </select>
                                </div>
                                <div class="form-group mb-2">
                                    <textarea name="clearance_notes" class="form-control" rows="2" placeholder="Add clearance notes">{{ $profile->clearance_notes }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Save Clearance Tag</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@stop
