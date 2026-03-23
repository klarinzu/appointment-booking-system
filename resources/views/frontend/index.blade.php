<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $setting->meta_title ?: 'LNU DOCUMATE' }}</title>
    <meta name="description" content="{{ $setting->meta_description }}">
    <meta name="keywords" content="{{ $setting->meta_keywords }}">
    <link rel="icon" type="image/png" href="{{ asset('uploads/images/lnu logo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('uploads/images/lnu logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=REM:opsz,wght@8..144,600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #16203a;
            --muted: #55637f;
            --line: #d7dced;
            --brand: #0a177d;
            --brand-dark: #050a4d;
            --brand-bright: #243dbe;
            --surface: #f6f3eb;
            --card: #ffffff;
            --surface-soft: #fbfbfe;
            --accent: #d4a63f;
            --accent-soft: #f4e4ad;
            --teal: #10827c;
            --success: #1d7d60;
            --shadow: 0 22px 54px rgba(10, 23, 125, 0.12);
            --shadow-soft: 0 14px 30px rgba(10, 23, 125, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Poppins", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top right, rgba(212, 166, 63, 0.2), transparent 24%),
                radial-gradient(circle at top left, rgba(10, 23, 125, 0.08), transparent 30%),
                linear-gradient(180deg, #f6f2e7 0%, #f7f8fd 48%, #edf1ff 100%);
            line-height: 1.6;
            text-rendering: optimizeLegibility;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: "REM", Georgia, serif;
            letter-spacing: -0.02em;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        h1,
        h2,
        h3,
        h4,
        p,
        a,
        span,
        li,
        summary,
        strong,
        .btn,
        .chip,
        .quick-link,
        .code,
        .muted {
            overflow-wrap: anywhere;
        }

        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            padding: 18px 0;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(10, 23, 125, 0.08);
            box-shadow: 0 8px 22px rgba(10, 23, 125, 0.05);
        }

        .topbar .shell,
        .quick-links,
        .hero-actions,
        .hero-stats,
        .feature-grid,
        .transactions-grid,
        .guide-grid,
        .office-list,
        .handbook-grid,
        .performance-grid,
        .process-grid {
            display: grid;
            gap: 16px;
        }

        .topbar .shell {
            display: flex;
            gap: 16px;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .topbar .shell > *,
        .hero-panel > *,
        .section-head > *,
        .feature-grid > *,
        .transactions-grid > *,
        .guide-grid > *,
        .office-list > *,
        .handbook-grid > *,
        .performance-grid > *,
        .process-grid > * {
            min-width: 0;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--brand-dark);
        }

        .brand-logo-image {
            width: 54px;
            height: 54px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .brand-copy {
            display: inline-flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .brand-copy strong {
            font-family: "REM", Georgia, serif;
            font-size: 1rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 12px 20px;
            font-weight: 800;
            border: 1px solid transparent;
            transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease, color 0.18s ease;
            white-space: normal;
            text-align: center;
            min-height: 48px;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn.primary {
            background: linear-gradient(135deg, var(--brand-dark), var(--brand) 55%, var(--brand-bright));
            color: #fff;
            box-shadow: 0 14px 28px rgba(10, 23, 125, 0.18);
        }

        .btn.secondary {
            background: rgba(255, 255, 255, 0.96);
            border-color: rgba(10, 23, 125, 0.14);
            color: var(--brand-dark);
            box-shadow: 0 10px 20px rgba(10, 23, 125, 0.05);
        }

        .hero {
            padding: 36px 0 26px;
        }

        .hero-panel {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.85fr);
            gap: 24px;
        }

        .hero-copy,
        .hero-side,
        .section-card,
        .feature-card,
        .transaction-card,
        .process-card,
        .guide-card,
        .office-card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 28px;
            box-shadow: var(--shadow);
        }

        .hero-copy {
            position: relative;
            overflow: hidden;
            padding: 38px;
            color: #fff;
            background:
                radial-gradient(circle at top right, rgba(212, 166, 63, 0.28), transparent 22%),
                linear-gradient(145deg, rgba(5, 10, 77, 0.99), rgba(10, 23, 125, 0.97) 52%, rgba(36, 61, 190, 0.94));
            border-color: rgba(212, 166, 63, 0.28);
        }

        .hero-copy::after {
            content: "";
            position: absolute;
            inset: auto -90px -90px auto;
            width: 260px;
            height: 260px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-size: 0.76rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.82);
        }

        .section-eyebrow {
            color: var(--brand);
        }

        h1 {
            margin: 14px 0 16px;
            font-size: clamp(2.35rem, 5vw, 4.45rem);
            line-height: 0.98;
            max-width: 12ch;
        }

        .lead {
            max-width: 740px;
            font-size: 1.05rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-actions {
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            margin-top: 28px;
        }

        .hero-stats {
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            margin-top: 28px;
        }

        .stat {
            padding: 18px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(6px);
        }

        .stat strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1.9rem;
        }

        .hero-side {
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 255, 0.94));
        }

        .hero-side h2,
        .section-head h2 {
            margin: 0;
            font-size: 1.45rem;
        }

        .muted {
            color: var(--muted);
            line-height: 1.7;
        }

        .chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid rgba(10, 23, 125, 0.12);
            background: linear-gradient(180deg, rgba(10, 23, 125, 0.06), rgba(212, 166, 63, 0.08));
            color: var(--brand-dark);
            font-size: 0.88rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .quick-links {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 18px;
        }

        .quick-link {
            padding: 16px 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid var(--line);
            box-shadow: 0 14px 32px rgba(16, 33, 59, 0.06);
            font-weight: 700;
            color: var(--brand-dark);
        }

        .section {
            padding: 18px 0 46px;
        }

        .section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 16px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .section-card {
            padding: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 255, 0.94));
        }

        .feature-grid,
        .performance-grid,
        .handbook-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .feature-card,
        .process-card,
        .guide-card,
        .office-card {
            padding: 22px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 255, 0.94));
            box-shadow: var(--shadow-soft);
        }

        .feature-card h3,
        .transaction-card h3,
        .process-card h3,
        .guide-card h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.12rem;
        }

        .search-bar {
            padding: 14px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: var(--shadow-soft);
        }

        .search-bar input {
            width: 100%;
            border: 0;
            outline: 0;
            background: transparent;
            font: inherit;
            color: var(--ink);
            padding: 4px 2px;
        }

        .transactions-grid {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }

        .transaction-card {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(249, 250, 255, 0.94));
        }

        .transaction-top {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: start;
        }

        .code {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(10, 23, 125, 0.09), rgba(212, 166, 63, 0.12));
            color: var(--brand-dark);
            font-weight: 800;
            font-size: 0.84rem;
        }

        .transaction-meta {
            display: grid;
            gap: 10px;
            color: var(--muted);
        }

        .transaction-meta strong {
            color: var(--ink);
            display: block;
            margin-bottom: 4px;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .transaction-card details {
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid var(--line);
            background: linear-gradient(180deg, rgba(246, 243, 235, 0.76), rgba(250, 251, 255, 0.92));
        }

        .transaction-card summary {
            cursor: pointer;
            font-weight: 800;
        }

        .transaction-card ol,
        .transaction-card ul {
            margin: 12px 0 0;
            padding-left: 18px;
            color: var(--muted);
        }

        .process-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .process-number {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(10, 23, 125, 0.12), rgba(212, 166, 63, 0.14));
            color: var(--brand-dark);
            font-weight: 800;
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .guide-grid {
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 0.9fr);
        }

        .office-list {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .highlight {
            padding: 18px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(212, 166, 63, 0.16), rgba(10, 23, 125, 0.07));
            border: 1px solid rgba(212, 166, 63, 0.24);
        }

        .empty-message {
            display: none;
            padding: 18px 20px;
            border-radius: 22px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            color: var(--muted);
        }

        footer {
            padding: 16px 0 32px;
            color: var(--muted);
            text-align: center;
            font-size: 0.95rem;
        }

        @media (max-width: 980px) {
            .hero-panel,
            .guide-grid,
            .hero-stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .shell {
                width: min(100% - 20px, 1180px);
            }

            .hero-copy,
            .hero-side,
            .section-card {
                padding: 22px;
            }

            h1 {
                font-size: 2.35rem;
            }
        }
    </style>
    @if ($setting->header)
        {!! $setting->header !!}
    @endif
</head>

<body>
    <header class="topbar">
        <div class="shell">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset('uploads/images/lnu logo.png') }}" alt="LNU DOCUMATE Logo" class="brand-logo-image">
                <span class="brand-copy">
                    <strong>LNU DOCUMATE</strong>
                    <span class="brand-sub">Leyte Normal University</span>
                </span>
            </a>
            <nav class="nav">
                @auth
                    <a class="btn secondary" href="{{ route('dashboard') }}">Dashboard</a>
                    <a class="btn primary" href="{{ route('documate.transactions.index') }}">Open Transactions</a>
                @else
                    <a class="btn secondary" href="{{ route('login') }}">Login</a>
                    <a class="btn primary" href="{{ route('register') }}">Sign Up Account</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="shell">
                <div class="hero-panel">
                    <div class="hero-copy">
                        <div class="eyebrow">Leyte Normal University | Vice-President for Student Development</div>
                        <h1>Student transactions made easier to understand and easier to finish.</h1>
                        <p class="lead">
                            LNU DOCUMATE keeps VPSD transaction requests, official forms, signatory guidance, appointment schedules,
                            status tracking, and handbook support in one place so students do not have to guess their next step.
                        </p>

                        <div class="hero-actions">
                            @auth
                                <a class="btn primary" href="{{ route('dashboard') }}">Go to My Homepage</a>
                                <a class="btn secondary" href="{{ route('documate.handbook.index') }}">Open Student Handbook</a>
                            @else
                                <a class="btn primary" href="{{ route('register') }}">Sign Up Account</a>
                                <a class="btn secondary" href="{{ route('login') }}">Sign In</a>
                            @endauth
                        </div>

                        <div class="hero-stats">
                            <div class="stat">
                                <strong>{{ $transactionTypes->count() }}</strong>
                                available transaction forms
                            </div>
                            <div class="stat">
                                <strong>{{ count($statusLabels) }}</strong>
                                progress stages students can follow
                            </div>
                            <div class="stat">
                                <strong>{{ data_get(config('documate.appointments'), 'daily', 50) }}</strong>
                                students accommodated each day
                            </div>
                        </div>
                    </div>

                    <aside class="hero-side">
                        <h2>What students can do inside LNU DOCUMATE</h2>
                        <p class="muted">
                            Register once, request only the forms you are eligible for, print official documents after approval,
                            follow the required signatories, and reserve an appointment when the form is ready.
                        </p>

                        <div class="highlight">
                            <strong>Appointment policy</strong>
                            <p class="muted" style="margin-bottom: 0;">
                                Each day accommodates 25 students in the morning and 25 in the afternoon for a total of 50 scheduled visits.
                            </p>
                        </div>

                        <div class="chip-row">
                            <span class="chip">Role-based access</span>
                            <span class="chip">Approval-gated forms</span>
                            <span class="chip">Clearance-aware validation</span>
                            <span class="chip">Printable official forms</span>
                        </div>
                    </aside>
                </div>

                <div class="quick-links">
                    <a href="#catalog" class="quick-link">Browse transaction forms</a>
                    <a href="#process" class="quick-link">Understand the process</a>
                    <a href="#guidance" class="quick-link">Find signatory offices</a>
                    <a href="#performance" class="quick-link">See system capabilities</a>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <div class="eyebrow section-eyebrow">Core Features</div>
                        <h2>Designed to reduce confusion during student transactions</h2>
                    </div>
                    <p class="muted" style="max-width: 540px;">
                        Instead of separate steps scattered across offices and paper copies, LNU DOCUMATE shows the transaction rules, progress stages,
                        and schedule expectations in one consistent student-facing system.
                    </p>
                </div>

                <div class="feature-grid">
                    <article class="feature-card">
                        <h3>Profile-Based Forms</h3>
                        <p class="muted">Student records are collected during registration so official forms can be auto-filled using the stored profile data.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Eligibility and Clearance Checks</h3>
                        <p class="muted">Students only see request options they can actually pursue, and clearance holds immediately affect transaction access.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Guided Appointment Completion</h3>
                        <p class="muted">After approval and manual routing, students can reserve a visit slot and track the request all the way to completion.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="catalog">
            <div class="shell">
                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <div class="eyebrow section-eyebrow">Transaction Catalog</div>
                            <h2>Find the form you need before you request it</h2>
                        </div>
                        <p class="muted" style="max-width: 520px;">
                            Search by form code, title, signatory, or keyword to narrow the catalog quickly, then review the embedded instructions for each form.
                        </p>
                    </div>

                    <div class="search-bar" style="margin-bottom: 18px;">
                        <input id="transaction-search" type="text" placeholder="Search transaction forms by code, title, signatory, or keyword">
                    </div>

                    <div class="transactions-grid" id="transaction-grid">
                        @foreach ($transactionTypes as $transactionType)
                            <article class="transaction-card transaction-item" data-search="{{ strtolower(implode(' ', [
                                $transactionType->code,
                                $transactionType->name,
                                $transactionType->description,
                                collect($transactionType->required_signatories ?? [])->implode(' '),
                                collect($transactionType->workflow_steps ?? [])->implode(' '),
                            ])) }}">
                                <div class="transaction-top">
                                    <div>
                                        <div class="code">{{ $transactionType->code }}</div>
                                        <h3>{{ $transactionType->name }}</h3>
                                    </div>
                                    <span class="chip">{{ count($transactionType->workflow_steps ?? []) }} steps</span>
                                </div>

                                <p class="muted" style="margin: 0;">{{ $transactionType->description }}</p>

                                <div class="transaction-meta">
                                    <div>
                                        <strong>Required signatories</strong>
                                        {{ collect($transactionType->required_signatories)->implode(', ') ?: 'None listed' }}
                                    </div>
                                    <div>
                                        <strong>Notarization</strong>
                                        {{ $transactionType->requires_notary ? 'Required before the scheduled appointment' : 'Not required' }}
                                    </div>
                                </div>

                                <details>
                                    <summary>View workflow steps</summary>
                                    <ol>
                                        @foreach ($transactionType->workflow_steps ?? [] as $step)
                                            <li>{{ $step }}</li>
                                        @endforeach
                                    </ol>
                                </details>
                            </article>
                        @endforeach
                    </div>

                    <div class="empty-message" id="transaction-empty">
                        No transaction form matched your search. Try a form code like F-SDM-004, a signatory office, or part of the form title.
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="process">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <div class="eyebrow section-eyebrow">Student Process</div>
                        <h2>The LNU DOCUMATE flow in four clear stages</h2>
                    </div>
                </div>

                <div class="process-grid">
                    <article class="process-card">
                        <div class="process-number">1</div>
                        <h3>Register and complete the profile</h3>
                        <p class="muted">Students create an account once, then LNU DOCUMATE keeps the personal and academic details ready for future forms.</p>
                    </article>
                    <article class="process-card">
                        <div class="process-number">2</div>
                        <h3>Request an eligible transaction</h3>
                        <p class="muted">The system checks profile completeness, clearance state, and existing open requests before accepting a new request.</p>
                    </article>
                    <article class="process-card">
                        <div class="process-number">3</div>
                        <h3>Print and route the official form</h3>
                        <p class="muted">After admin approval, the official form becomes accessible for printing, signature collection, and notarization when needed.</p>
                    </article>
                    <article class="process-card">
                        <div class="process-number">4</div>
                        <h3>Book the office appointment</h3>
                        <p class="muted">Students choose an available morning or afternoon slot and bring the completed original form to LNU DOCUMATE on schedule.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="section" id="guidance">
            <div class="shell guide-grid">
                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <div class="eyebrow section-eyebrow">Office Guidance</div>
                            <h2>Common signatory and support offices</h2>
                        </div>
                    </div>

                    <div class="office-list">
                        @foreach ($officeLocations as $office)
                            <article class="office-card">
                                <h3>{{ $office['name'] }}</h3>
                                <p class="muted" style="margin-bottom: 0;">{{ $office['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>

                <div class="section-card">
                    <div class="section-head">
                        <div>
                            <div class="eyebrow section-eyebrow">Student Handbook</div>
                            <h2>{{ $handbook['title'] ?? 'University Student Handbook' }}</h2>
                        </div>
                    </div>
                    <p class="muted">{{ $handbook['summary'] ?? '' }}</p>
                    <div class="handbook-grid">
                        @foreach (($handbook['sections'] ?? []) as $section)
                            <article class="guide-card">
                                <h3>{{ $section['title'] }}</h3>
                                <p class="muted" style="margin-bottom: 0;">{{ $section['body'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="section" id="performance">
            <div class="shell">
                <div class="section-head">
                    <div>
                        <div class="eyebrow section-eyebrow">Performance Requirements</div>
                        <h2>Built for quick, mobile-friendly transaction handling</h2>
                    </div>
                </div>

                <div class="performance-grid">
                    <article class="feature-card">
                        <h3>Quick System Response</h3>
                        <p class="muted">Login, form selection, dashboard access, and record retrieval are designed to respond promptly during normal daily use.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Mobile Device Compatibility</h3>
                        <p class="muted">Core LNU DOCUMATE actions stay usable across desktop, tablet, and smartphone browsers so students are not limited to one device type.</p>
                    </article>
                    <article class="feature-card">
                        <h3>Prompt Notifications</h3>
                        <p class="muted">Students and reviewers receive timely in-system feedback for transaction requests, approval decisions, clearance changes, and status updates.</p>
                    </article>
                </div>
            </div>
        </section>
    </main>

    <footer>
        LNU DOCUMATE | Leyte Normal University | Office of the Vice-President for Student Development
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('transaction-search');
            const items = Array.from(document.querySelectorAll('.transaction-item'));
            const emptyState = document.getElementById('transaction-empty');

            if (!searchInput || !items.length || !emptyState) {
                return;
            }

            const filterCatalog = function() {
                const query = searchInput.value.trim().toLowerCase();
                let visible = 0;

                items.forEach(function(item) {
                    const haystack = item.dataset.search || '';
                    const matches = !query || haystack.includes(query);
                    item.style.display = matches ? '' : 'none';

                    if (matches) {
                        visible += 1;
                    }
                });

                emptyState.style.display = visible ? 'none' : 'block';
            };

            searchInput.addEventListener('input', filterCatalog);
        });
    </script>

    @if ($setting->footer)
        {!! $setting->footer !!}
    @endif
</body>

</html>
