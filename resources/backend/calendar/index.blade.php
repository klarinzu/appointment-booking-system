@extends('adminlte::page')

@php
    $appointmentCapacity = config('documate.appointments');
@endphp

@section('title', 'Scheduled Calendar')

@section('css')
    <style>
        .doc-calendar-shell {
            display: grid;
            gap: 1rem;
        }

        .doc-calendar-toolbar {
            display: flex;
            gap: 0.75rem;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .doc-calendar-toolbar > *,
        .doc-calendar-meta > *,
        .doc-calendar-grid > * {
            min-width: 0;
        }

        .doc-calendar-nav {
            display: inline-flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .doc-calendar-nav .btn {
            min-width: 44px;
        }

        .doc-calendar-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 0.75rem;
        }

        .doc-calendar-stat {
            border: 1px solid rgba(7, 3, 114, 0.09);
            border-radius: 18px;
            padding: 0.95rem 1rem;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 12px 24px rgba(10, 23, 125, 0.06);
        }

        .doc-calendar-stat strong {
            display: block;
            font-size: 1.35rem;
            color: var(--doc-primary-dark);
            line-height: 1.1;
            margin-bottom: 0.25rem;
        }

        .doc-calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 0.75rem;
        }

        .doc-calendar-weekday {
            text-align: center;
            font-size: 0.82rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--doc-text-muted);
            font-weight: 700;
        }

        .doc-calendar-day {
            min-height: 146px;
            border: 1px solid rgba(7, 3, 114, 0.08);
            border-radius: 20px;
            padding: 0.9rem;
            background: linear-gradient(180deg, #ffffff 0%, #f7f8fe 100%);
            display: grid;
            gap: 0.55rem;
            align-content: start;
            box-shadow: 0 10px 22px rgba(10, 23, 125, 0.05);
        }

        .doc-calendar-day.is-outside {
            opacity: 0.4;
            background: #fafbff;
        }

        .doc-calendar-day.is-today {
            border-color: rgba(8, 0, 242, 0.3);
            box-shadow: 0 10px 24px rgba(8, 0, 242, 0.08);
        }

        .doc-calendar-day.is-busy {
            background:
                radial-gradient(circle at top right, rgba(255, 170, 0, 0.14), transparent 28%),
                linear-gradient(180deg, #ffffff 0%, #f8f9ff 100%);
        }

        .doc-calendar-day.is-full {
            border-color: rgba(128, 0, 0, 0.18);
            background:
                radial-gradient(circle at top right, rgba(128, 0, 0, 0.12), transparent 28%),
                linear-gradient(180deg, #fffefe 0%, #fff8f8 100%);
        }

        .doc-calendar-day-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .doc-calendar-day-number {
            font-size: 1rem;
            font-weight: 800;
            color: var(--doc-primary-dark);
        }

        .doc-calendar-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 0.24rem 0.52rem;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: rgba(7, 3, 114, 0.08);
            color: var(--doc-primary-dark);
        }

        .doc-calendar-badge.is-full {
            background: rgba(128, 0, 0, 0.12);
            color: #7a1010;
        }

        .doc-calendar-day-body {
            display: grid;
            gap: 0.4rem;
            font-size: 0.83rem;
        }

        .doc-calendar-line {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            color: var(--doc-text);
            align-items: flex-start;
        }

        .doc-calendar-line span:first-child {
            color: var(--doc-text-muted);
        }

        .doc-calendar-line span:last-child {
            text-align: right;
            font-weight: 700;
        }

        .doc-calendar-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 130px;
            border: 1px dashed rgba(7, 3, 114, 0.12);
            border-radius: 20px;
            color: var(--doc-text-muted);
            background: rgba(255, 255, 255, 0.92);
            text-align: center;
            padding: 1rem;
        }

        .doc-calendar-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--doc-text-muted);
            font-size: 0.84rem;
        }

        .doc-calendar-status::before {
            content: '';
            width: 0.6rem;
            height: 0.6rem;
            border-radius: 999px;
            background: #1f9d55;
            box-shadow: 0 0 0 6px rgba(31, 157, 85, 0.12);
        }

        @media (max-width: 1199.98px) {
            .doc-calendar-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 991.98px) {
            .doc-calendar-grid {
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            }

            .doc-calendar-weekday {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .doc-calendar-meta,
            .doc-calendar-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@stop

@section('content_header')
    <div class="doc-page-hero">
        <div class="doc-hero-copy">
            <div class="doc-hero-eyebrow">DOCUMATE Calendar</div>
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start">
                <div class="pr-lg-4 mb-4 mb-lg-0">
                    <h1 class="mb-2">Scheduled Transaction Calendar</h1>
                    <p class="doc-note mb-3">{{ $calendarScopeLabel }}</p>
                    <p class="mb-0" style="max-width: 780px;">
                        Monitor the daily booking load, review busy dates, and keep track of how many DOCUMATE transactions are already scheduled in real time.
                    </p>
                </div>
                <div class="doc-hero-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Back to Dashboard</a>
                    <a href="{{ route('documate.transactions.index') }}" class="btn btn-primary">Open Transactions</a>
                </div>
            </div>

            <div class="doc-inline-stats">
                <div class="doc-inline-stat">
                    <strong>{{ data_get($appointmentCapacity, 'daily', 50) }}</strong>
                    <span class="doc-note">total students accommodated per day</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ data_get($appointmentCapacity, 'morning', 25) }}/{{ data_get($appointmentCapacity, 'afternoon', 25) }}</strong>
                    <span class="doc-note">morning and afternoon capacity split</span>
                </div>
                <div class="doc-inline-stat">
                    <strong>{{ $calendarRefreshSeconds }}s</strong>
                    <span class="doc-note">automatic refresh interval</span>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="doc-table-tools">
                <div>
                    <h3 class="card-title mb-1">Monthly Appointment Load</h3>
                    <div class="doc-note small">
                        Use this dedicated calendar view to see booked transactions by day and monitor remaining capacity.
                    </div>
                </div>
                <div class="doc-calendar-status" id="doc-calendar-status">Auto-refresh every {{ $calendarRefreshSeconds }} seconds</div>
            </div>
        </div>
        <div class="card-body">
            <div class="doc-calendar-shell">
                <div class="doc-calendar-toolbar">
                    <div class="doc-calendar-nav">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="doc-calendar-prev" aria-label="Previous month">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div>
                            <div class="doc-kicker mb-1">Viewing month</div>
                            <div class="font-weight-bold" id="doc-calendar-month-label">{{ \Carbon\Carbon::createFromFormat('Y-m', $calendarMonthKey)->format('F Y') }}</div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="doc-calendar-next" aria-label="Next month">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="doc-calendar-today">Jump to Current Month</button>
                </div>

                <div class="doc-calendar-meta">
                    <div class="doc-calendar-stat">
                        <strong id="doc-calendar-total">0</strong>
                        <span class="doc-note">booked transactions this month</span>
                    </div>
                    <div class="doc-calendar-stat">
                        <strong id="doc-calendar-active">0</strong>
                        <span class="doc-note">still in appointment scheduled status</span>
                    </div>
                    <div class="doc-calendar-stat">
                        <strong id="doc-calendar-today-total">0</strong>
                        <span class="doc-note">transactions on today's date</span>
                    </div>
                    <div class="doc-calendar-stat">
                        <strong id="doc-calendar-busiest">0</strong>
                        <span class="doc-note" id="doc-calendar-busiest-label">busiest day count</span>
                    </div>
                </div>

                <div class="doc-calendar-grid" id="doc-calendar-weekdays">
                    <div class="doc-calendar-weekday">Sun</div>
                    <div class="doc-calendar-weekday">Mon</div>
                    <div class="doc-calendar-weekday">Tue</div>
                    <div class="doc-calendar-weekday">Wed</div>
                    <div class="doc-calendar-weekday">Thu</div>
                    <div class="doc-calendar-weekday">Fri</div>
                    <div class="doc-calendar-weekday">Sat</div>
                </div>

                <div class="doc-calendar-grid" id="doc-calendar-grid">
                    <div class="doc-calendar-empty" style="grid-column: 1 / -1;">Loading scheduled transactions...</div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const feedUrl = @json($calendarFeedUrl);
            const refreshSeconds = @json($calendarRefreshSeconds);
            const grid = document.getElementById('doc-calendar-grid');
            const monthLabel = document.getElementById('doc-calendar-month-label');
            const totalTarget = document.getElementById('doc-calendar-total');
            const activeTarget = document.getElementById('doc-calendar-active');
            const todayTarget = document.getElementById('doc-calendar-today-total');
            const busiestTarget = document.getElementById('doc-calendar-busiest');
            const busiestLabelTarget = document.getElementById('doc-calendar-busiest-label');
            const statusTarget = document.getElementById('doc-calendar-status');
            const prevButton = document.getElementById('doc-calendar-prev');
            const nextButton = document.getElementById('doc-calendar-next');
            const todayButton = document.getElementById('doc-calendar-today');
            let currentMonth = @json($calendarMonthKey);

            if (!grid || !monthLabel) {
                return;
            }

            const monthFormatter = new Intl.DateTimeFormat('en-US', {
                month: 'long',
                year: 'numeric'
            });

            const shortDateFormatter = new Intl.DateTimeFormat('en-US', {
                month: 'short',
                day: 'numeric'
            });

            const toMonthKey = function(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');

                return `${year}-${month}`;
            };

            const parseMonth = function(monthKey) {
                return new Date(`${monthKey}-01T00:00:00`);
            };

            const renderSummary = function(summary) {
                totalTarget.textContent = summary.total_booked ?? 0;
                activeTarget.textContent = summary.active_scheduled ?? 0;
                todayTarget.textContent = summary.today_total ?? 0;

                if (summary.busiest_day) {
                    busiestTarget.textContent = summary.busiest_day.total;
                    busiestLabelTarget.textContent = `busiest day: ${shortDateFormatter.format(new Date(`${summary.busiest_day.date}T00:00:00`))}`;
                } else {
                    busiestTarget.textContent = 0;
                    busiestLabelTarget.textContent = 'busiest day count';
                }
            };

            const renderCalendar = function(monthKey, days) {
                const monthDate = parseMonth(monthKey);
                const today = new Date();
                const todayKey = toMonthKey(today) + '-' + String(today.getDate()).padStart(2, '0');
                const firstDayOfMonth = new Date(monthDate.getFullYear(), monthDate.getMonth(), 1);
                const startCursor = new Date(firstDayOfMonth);
                startCursor.setDate(firstDayOfMonth.getDate() - firstDayOfMonth.getDay());

                const dayMap = new Map(days.map(function(day) {
                    return [day.date, day];
                }));

                monthLabel.textContent = monthFormatter.format(monthDate);
                grid.innerHTML = '';

                for (let index = 0; index < 42; index += 1) {
                    const cursor = new Date(startCursor);
                    cursor.setDate(startCursor.getDate() + index);

                    const dateKey = `${cursor.getFullYear()}-${String(cursor.getMonth() + 1).padStart(2, '0')}-${String(cursor.getDate()).padStart(2, '0')}`;
                    const dayData = dayMap.get(dateKey);
                    const inCurrentMonth = cursor.getMonth() === monthDate.getMonth();
                    const isToday = dateKey === todayKey;
                    const total = dayData ? dayData.total : 0;
                    const classes = ['doc-calendar-day'];

                    if (!inCurrentMonth) {
                        classes.push('is-outside');
                    }

                    if (isToday) {
                        classes.push('is-today');
                    }

                    if (total > 0) {
                        classes.push('is-busy');
                    }

                    if (dayData && dayData.is_full) {
                        classes.push('is-full');
                    }

                    const badge = total > 0
                        ? `<span class="doc-calendar-badge ${dayData && dayData.is_full ? 'is-full' : ''}">${total} booked</span>`
                        : '';

                    const details = dayData
                        ? `
                            <div class="doc-calendar-day-body">
                                <div class="doc-calendar-line"><span>AM</span><strong>${dayData.morning}</strong></div>
                                <div class="doc-calendar-line"><span>PM</span><strong>${dayData.afternoon}</strong></div>
                                <div class="doc-calendar-line"><span>Active</span><strong>${dayData.active_scheduled}</strong></div>
                                <div class="doc-calendar-line"><span>Remaining</span><strong>${dayData.remaining}</strong></div>
                            </div>
                        `
                        : `
                            <div class="doc-calendar-day-body">
                                <div class="doc-note small">No scheduled transactions.</div>
                            </div>
                        `;

                    grid.insertAdjacentHTML('beforeend', `
                        <div class="${classes.join(' ')}">
                            <div class="doc-calendar-day-head">
                                <div class="doc-calendar-day-number">${cursor.getDate()}</div>
                                ${badge}
                            </div>
                            ${details}
                        </div>
                    `);
                }
            };

            const renderError = function(message) {
                grid.innerHTML = `<div class="doc-calendar-empty" style="grid-column: 1 / -1;">${message}</div>`;
            };

            const loadMonth = function(monthKey, silent = false) {
                currentMonth = monthKey;

                if (!silent) {
                    renderError('Loading scheduled transactions...');
                }

                fetch(`${feedUrl}?month=${encodeURIComponent(monthKey)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Unable to load scheduled calendar.');
                        }

                        return response.json();
                    })
                    .then(function(data) {
                        renderSummary(data.summary || {});
                        renderCalendar(data.month || monthKey, data.days || []);

                        const generatedAt = data.generated_at ? new Date(data.generated_at) : new Date();
                        statusTarget.textContent = `Live counts refreshed ${generatedAt.toLocaleTimeString()}`;
                    })
                    .catch(function() {
                        renderError('The calendar could not be loaded right now. Please refresh the page.');
                        statusTarget.textContent = 'Calendar refresh failed';
                    });
            };

            prevButton.addEventListener('click', function() {
                const currentDate = parseMonth(currentMonth);
                currentDate.setMonth(currentDate.getMonth() - 1);
                loadMonth(toMonthKey(currentDate));
            });

            nextButton.addEventListener('click', function() {
                const currentDate = parseMonth(currentMonth);
                currentDate.setMonth(currentDate.getMonth() + 1);
                loadMonth(toMonthKey(currentDate));
            });

            todayButton.addEventListener('click', function() {
                loadMonth(toMonthKey(new Date()));
            });

            loadMonth(currentMonth);
            window.setInterval(function() {
                loadMonth(currentMonth, true);
            }, refreshSeconds * 1000);
        });
    </script>
@endpush
