<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $payload = $transaction->form_payload ?? [];
        $isExample = $isExample ?? false;
        $backUrl = $backUrl ?? ($isExample ? route('documate.transactions.index') : route('documate.transactions.show', $transaction));
        $canDownload = !$isExample && $transaction->exists;
    @endphp
    <title>{{ $transaction->transactionType?->code }} - {{ $isExample ? 'DOCUMATE Example Form' : 'LNU DOCUMATE Official Form' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=REM:opsz,wght@8..144,600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --form-ink: #16203a;
            --form-muted: #586784;
            --form-line: #d7dced;
            --form-brand: #0a177d;
            --form-brand-dark: #050a4d;
            --form-brand-bright: #243dbe;
            --form-gold: #d4a63f;
            --form-surface: #f6f3eb;
            --form-shadow: 0 22px 54px rgba(10, 23, 125, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", Arial, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(212, 166, 63, 0.18), transparent 24%),
                linear-gradient(180deg, #f6f2e7 0%, #eef1ff 100%);
            color: var(--form-ink);
            margin: 0;
            padding: 24px;
            line-height: 1.6;
        }

        .page {
            max-width: 980px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid var(--form-line);
            border-radius: 30px;
            box-shadow: var(--form-shadow);
            overflow: hidden;
        }

        .toolbar,
        .header,
        .section,
        .signatories {
            padding: 24px 32px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
            border-bottom: 1px solid #e4e8f5;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(247, 249, 255, 0.96));
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 999px;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: var(--form-ink);
            background: #ffffff;
            font-weight: 700;
            white-space: normal;
            text-align: center;
        }

        .btn.primary {
            background: linear-gradient(135deg, var(--form-brand-dark), var(--form-brand) 55%, var(--form-brand-bright));
            color: #ffffff;
            border-color: var(--form-brand);
        }

        .header {
            border-bottom: 4px solid var(--form-gold);
            background:
                radial-gradient(circle at top right, rgba(212, 166, 63, 0.16), transparent 22%),
                linear-gradient(180deg, rgba(10, 23, 125, 0.04), #ffffff 74%);
        }

        .header-top {
            display: flex;
            gap: 18px;
            align-items: center;
            margin-bottom: 14px;
        }

        .header-logo {
            width: 78px;
            height: 78px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .eyebrow {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--form-brand);
        }

        .masthead {
            display: grid;
            gap: 4px;
            margin-bottom: 16px;
        }

        .masthead strong {
            font-family: "REM", Georgia, serif;
            font-size: 1.24rem;
            color: var(--form-brand-dark);
        }

        .masthead span {
            color: var(--form-muted);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        h1 {
            margin: 8px 0 6px;
            font-size: 30px;
            font-family: "REM", Georgia, serif;
            color: var(--form-brand-dark);
            line-height: 1.12;
        }

        .subtext {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem 0.7rem;
            color: var(--form-muted);
            font-size: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .field {
            border: 1px solid var(--form-line);
            border-radius: 16px;
            padding: 14px 16px;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            box-shadow: 0 10px 22px rgba(10, 23, 125, 0.05);
        }

        .field.is-full {
            grid-column: 1 / -1;
        }

        .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--form-muted);
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: 0.06em;
        }

        .value {
            min-height: 22px;
            font-size: 15px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .section-title {
            margin: 0 0 12px;
            font-size: 18px;
            font-family: "REM", Georgia, serif;
            color: var(--form-brand-dark);
        }

        ol,
        ul {
            margin: 0;
            padding-left: 20px;
            color: var(--form-muted);
        }

        li + li {
            margin-top: 8px;
        }

        .note-box {
            margin-top: 12px;
            border: 1px dashed rgba(166, 120, 22, 0.65);
            border-radius: 12px;
            padding: 14px 16px;
            background: linear-gradient(135deg, rgba(212, 166, 63, 0.12), rgba(10, 23, 125, 0.05));
        }

        .signatories {
            border-top: 1px solid #e4e8f5;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-top: 16px;
        }

        .signature-box {
            padding-top: 42px;
        }

        .signature-line {
            border-top: 1px solid var(--form-brand);
            padding-top: 8px;
            font-size: 14px;
            text-align: center;
        }

        .footer {
            padding: 0 32px 28px;
            color: var(--form-muted);
            font-size: 13px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .page {
                box-shadow: none;
                border: none;
                max-width: none;
            }

            .toolbar {
                display: none;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .toolbar,
            .header,
            .section,
            .signatories,
            .footer {
                padding-left: 18px;
                padding-right: 18px;
            }

            .grid,
            .signature-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="toolbar">
            <a href="{{ $backUrl }}" class="btn">{{ $isExample ? 'Back to Transactions' : 'Back to Transaction' }}</a>
            @if (!$isExample && $transaction->transactionType)
                <a href="{{ route('documate.transactions.example', $transaction->transactionType) }}" class="btn">View Example</a>
            @endif
            @if ($canDownload)
                <a href="{{ route('documate.transactions.download', $transaction) }}" class="btn">Download HTML</a>
            @endif
            <button class="btn primary" type="button" onclick="window.print()">{{ $isExample ? 'Print Example' : 'Print Form' }}</button>
        </div>

        <div class="header">
            <div class="header-top">
                <img src="{{ asset('uploads/images/lnu logo.png') }}" alt="Leyte Normal University Logo" class="header-logo">
                <div class="masthead">
                    <strong>Leyte Normal University</strong>
                    <span>Office of the Vice-President for Student Development</span>
                    <span>{{ $isExample ? 'LNU DOCUMATE Example Transaction Form' : 'LNU DOCUMATE Official Transaction Form' }}</span>
                </div>
            </div>
            <div class="eyebrow">{{ $isExample ? 'Example Preview Only' : 'Student Transaction Record' }}</div>
            <h1>{{ $transaction->transactionType?->name }}</h1>
            <div class="subtext">
                @if ($isExample)
                    <span>Example Preview</span>
                @endif
                <span>{{ $transaction->transactionType?->code }}</span>
                <span>Reference {{ $transaction->reference_no }}</span>
                <span>Status {{ config('documate.statuses.' . $transaction->status) ?? $transaction->status }}</span>
            </div>
        </div>

        @if ($isExample)
            <div class="section" style="padding-bottom: 0;">
                <div class="note-box">
                    <strong>Example Preview Only:</strong>
                    This sample shows how the {{ $transaction->transactionType?->name }} form can look when student details are already auto-filled.
                    Use the actual official form for real requests, signatures, and scheduled submissions.
                </div>
            </div>
        @endif

        <div class="section">
            <h2 class="section-title">Student Record</h2>
            <div class="grid">
                <div class="field">
                    <span class="label">Student Name</span>
                    <div class="value">{{ $payload['student_name'] ?? $transaction->user?->name }}</div>
                </div>
                <div class="field">
                    <span class="label">Student Number</span>
                    <div class="value">{{ $payload['student_number'] ?? $transaction->user?->studentProfile?->student_number }}</div>
                </div>
                <div class="field">
                    <span class="label">Email Address</span>
                    <div class="value">{{ $payload['student_email'] ?? $transaction->user?->email }}</div>
                </div>
                <div class="field">
                    <span class="label">Phone Number</span>
                    <div class="value">{{ $payload['student_phone'] ?? $transaction->user?->phone }}</div>
                </div>
                <div class="field">
                    <span class="label">Course</span>
                    <div class="value">{{ $payload['course'] ?? $transaction->user?->studentProfile?->course }}</div>
                </div>
                <div class="field">
                    <span class="label">College</span>
                    <div class="value">{{ $payload['college'] ?? $transaction->user?->studentProfile?->college }}</div>
                </div>
                <div class="field">
                    <span class="label">Year Level</span>
                    <div class="value">{{ $payload['year_level'] ?? $transaction->user?->studentProfile?->year_level }}</div>
                </div>
                <div class="field">
                    <span class="label">Section</span>
                    <div class="value">{{ $payload['section'] ?? $transaction->user?->studentProfile?->section ?: 'N/A' }}</div>
                </div>
                <div class="field is-full">
                    <span class="label">Address</span>
                    <div class="value">{{ $payload['address'] ?? $transaction->user?->studentProfile?->address }}</div>
                </div>
                <div class="field">
                    <span class="label">Parent or Guardian</span>
                    <div class="value">{{ $payload['guardian_name'] ?? $transaction->user?->studentProfile?->guardian_name }}</div>
                </div>
                <div class="field">
                    <span class="label">Guardian Contact</span>
                    <div class="value">{{ $payload['guardian_contact'] ?? $transaction->user?->studentProfile?->guardian_contact }}</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 class="section-title">Embedded Instructions</h2>
            <ol>
                @foreach ($transaction->transactionType?->workflow_steps ?? [] as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>

            <div class="note-box">
                <strong>Student Notes:</strong>
                {{ $transaction->student_notes ?: 'No additional notes were submitted by the student.' }}
            </div>

            @if ($transaction->transactionType?->requires_notary)
                <div class="note-box">
                    <strong>Notary Reminder:</strong>
                    This transaction requires notarization before you attend your scheduled LNU DOCUMATE appointment.
                </div>
            @endif
        </div>

        <div class="section">
            <h2 class="section-title">Appointment Schedule</h2>
            <div class="grid">
                <div class="field">
                    <span class="label">Appointment Date</span>
                    <div class="value">{{ optional($transaction->appointment_date)->format('F d, Y') ?: 'Not scheduled yet' }}</div>
                </div>
                <div class="field">
                    <span class="label">Session</span>
                    <div class="value">{{ $transaction->appointment_session ? ucfirst($transaction->appointment_session) . ' Session' : 'Not scheduled yet' }}</div>
                </div>
            </div>
            <div class="note-box">
                <strong>Appointment Reminder:</strong>
                Bring the completed original form to your confirmed LNU DOCUMATE schedule. Daily capacity is 50 students, with 25 in the morning and 25 in the afternoon.
            </div>
        </div>

        <div class="signatories">
            <h2 class="section-title">Manual Signature Blocks</h2>
            <p class="subtext">Secure all required signatures before attending your scheduled LNU DOCUMATE appointment.</p>

            <div class="signature-grid">
                @forelse ($transaction->transactionType?->required_signatories ?? [] as $signatory)
                    <div class="signature-box">
                        <div class="signature-line">{{ $signatory }}</div>
                    </div>
                @empty
                    <div class="signature-box">
                        <div class="signature-line">Authorized Receiving Office</div>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="footer">
            {{ $isExample ? 'Example preview generated' : 'Generated' }} by LNU DOCUMATE on {{ now()->format('F d, Y h:i A') }}.
            This printable copy supports manual signing and in-person scheduled submission.
        </div>
    </div>
</body>

</html>
