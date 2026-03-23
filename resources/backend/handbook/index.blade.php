@extends('adminlte::page')

@section('title', 'Student Handbook')

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">Student Guidance</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <h1 class="mb-2">{{ $handbook['title'] ?? 'Student Handbook' }}</h1>
                    <p class="mb-0" style="max-width: 760px;">{{ $handbook['summary'] ?? 'DOCUMATE handbook guidance and office directory.' }}</p>
                </div>
                <div class="doc-hero-actions">
                    <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Open Transactions</a>
                </div>
            </div>

            <div class="doc-inline-stats">
                <div class="doc-inline-stat">
                    <strong>{{ count($handbook['sections'] ?? []) }}</strong>
                    <span class="doc-note">handbook sections available</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ count($officeLocations) }}</strong>
                    <span class="doc-note">office references for signatories</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ data_get(config('documate.appointments'), 'daily', 50) }}</strong>
                    <span class="doc-note">students accommodated per day</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Handbook Sections</h3>
                </div>
                <div class="card-body">
                    <div class="doc-card-grid">
                        @foreach ($handbook['sections'] ?? [] as $section)
                            <article class="doc-surface h-100">
                                <div class="doc-kicker mb-2">Policy Guidance</div>
                                <h3 class="h5 mb-2">{{ $section['title'] }}</h3>
                                <p class="doc-note mb-0">{{ $section['body'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">DOCUMATE Usage Reminders</h3>
                </div>
                <div class="card-body">
                    <div class="doc-card-grid">
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Approval Gate</div>
                            <div class="doc-note">Official transaction forms only become accessible after the required administrator approval.</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Manual Routing</div>
                            <div class="doc-note">Complete all manual signatures and any notarization requirement before attending the scheduled DOCUMATE appointment.</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Clearance Impact</div>
                            <div class="doc-note">Student officers can tag clearance status, and a hold can block transaction eligibility until it is resolved.</div>
                        </div>
                        <div class="doc-surface">
                            <div class="doc-kicker mb-2">Progress Tracking</div>
                            <div class="doc-note">Use each transaction detail page to monitor approval logs, workflow remarks, and appointment schedules.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title mb-0">Office Directory</h3>
                </div>
                <div class="card-body">
                    <div class="doc-detail-stack">
                        @foreach ($officeLocations as $office)
                            <div class="doc-directory-item">
                                <div class="font-weight-bold mb-1">{{ $office['name'] }}</div>
                                <div class="doc-note small">{{ $office['description'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @include('backend.partials.documate-chat')
        </div>
    </div>
@stop
