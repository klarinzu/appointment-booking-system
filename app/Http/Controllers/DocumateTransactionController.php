<?php

namespace App\Http\Controllers;

use App\Models\DocumateTransaction;
use App\Models\DocumateTransactionStatusLog;
use App\Models\DocumateTransactionType;
use App\Models\StudentProfile;
use App\Models\User;
use App\Notifications\DocumateTransactionNotification;
use App\Support\DocumateEligibility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DocumateTransactionController extends Controller
{
    public function index(Request $request, DocumateEligibility $eligibility)
    {
        $user = $request->user();

        if ($user->isStudentUser()) {
            $transactionTypes = DocumateTransactionType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function (DocumateTransactionType $type) use ($user, $eligibility) {
                    $type->eligibility = $eligibility->evaluate($user, $type);
                    return $type;
                });

            $transactions = $user->documateTransactions()
                ->with('transactionType', 'latestUpdate.actor', 'user.studentProfile')
                ->latest()
                ->get();

            $transactions = $this->sortTransactions($transactions, $request);

            return view('backend.transactions.index', [
                'transactionTypes' => $transactionTypes,
                'transactions' => $transactions,
                'mode' => 'student',
            ]);
        }

        $query = DocumateTransaction::query()
            ->with('transactionType', 'user.studentProfile', 'latestUpdate.actor')
            ->latest();

        if ($request->filled('transaction_type_id')) {
            $query->where('transaction_type_id', $request->integer('transaction_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', substr((string) $request->month, 5, 2))
                ->whereYear('created_at', substr((string) $request->month, 0, 4));
        }

        if ($request->filled('course')) {
            $query->whereHas('user.studentProfile', function ($builder) use ($request) {
                $builder->where('course', 'like', '%' . $request->string('course')->trim()->toString() . '%');
            });
        }

        $transactions = $this->sortTransactions($query->get(), $request);

        return view('backend.transactions.index', [
            'transactionTypes' => DocumateTransactionType::orderBy('sort_order')->get(),
            'transactions' => $transactions,
            'mode' => 'admin',
        ]);
    }

    public function store(Request $request, DocumateEligibility $eligibility)
    {
        $user = $request->user();
        abort_unless($user->isStudentUser(), 403);

        $data = $request->validate([
            'transaction_type_id' => 'required|exists:documate_transaction_types,id',
            'student_notes' => 'nullable|string|max:2000',
        ]);

        $transactionType = DocumateTransactionType::findOrFail($data['transaction_type_id']);
        $result = $eligibility->evaluate($user, $transactionType);

        if (!$result['eligible']) {
            return back()->withErrors(['transaction_type_id' => $result['reasons'][0]]);
        }

        $transaction = DocumateTransaction::create([
            'user_id' => $user->id,
            'transaction_type_id' => $transactionType->id,
            'reference_no' => 'DOC-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
            'status' => 'pending_admin_approval',
            'student_notes' => $data['student_notes'] ?? null,
            'requested_at' => now(),
            'form_payload' => $this->buildFormPayload($user, $transactionType),
            'last_updated_by' => $user->id,
        ]);

        $this->logStatus($transaction, $user, null, 'pending_admin_approval', 'Transaction request submitted.');

        $this->notifyReviewers(
            $transaction,
            'New DOCUMATE transaction request',
            $user->name . ' requested ' . $transactionType->name . '.',
            route('documate.transactions.show', $transaction)
        );

        return redirect()
            ->route('documate.transactions.show', $transaction)
            ->with('success', 'Your transaction request has been submitted for admin approval.');
    }

    public function show(Request $request, DocumateTransaction $transaction)
    {
        $this->authorizeTransaction($request->user(), $transaction);

        $transaction->load('transactionType', 'user.studentProfile', 'updates.actor');

        return view('backend.transactions.show', [
            'transaction' => $transaction,
            'statusLabels' => config('documate.statuses'),
            'appointmentCapacity' => config('documate.appointments'),
            'appointmentAvailability' => $this->buildAppointmentAvailability(),
        ]);
    }

    public function form(Request $request, DocumateTransaction $transaction)
    {
        $this->authorizeTransaction($request->user(), $transaction);
        abort_if(in_array($transaction->status, ['pending_admin_approval', 'rejected'], true), 403);

        $transaction->load('transactionType', 'user.studentProfile');

        return view('backend.transactions.form', [
            'transaction' => $transaction,
        ]);
    }

    public function exampleForm(Request $request, DocumateTransactionType $transactionType)
    {
        $user = $request->user();

        abort_unless(
            $user->isDocumateAdmin() || $user->isStudentOfficer() || $user->isStudentUser(),
            403
        );

        $previousUrl = url()->previous();
        $backUrl = filled($previousUrl) && $previousUrl !== $request->fullUrl()
            ? $previousUrl
            : route('documate.transactions.index');

        return view('backend.transactions.form', [
            'transaction' => $this->buildExampleTransaction($transactionType),
            'isExample' => true,
            'backUrl' => $backUrl,
        ]);
    }

    public function downloadForm(Request $request, DocumateTransaction $transaction)
    {
        $this->authorizeTransaction($request->user(), $transaction);
        abort_if(in_array($transaction->status, ['pending_admin_approval', 'rejected'], true), 403);

        $transaction->load('transactionType', 'user.studentProfile');
        $html = view('backend.transactions.form', ['transaction' => $transaction])->render();

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $transaction->reference_no . '.html"');
    }

    public function approve(Request $request, DocumateTransaction $transaction)
    {
        abort_unless($request->user()->isDocumateAdmin(), 403);
        abort_unless($transaction->status === 'pending_admin_approval', 403);

        $data = $request->validate([
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $fromStatus = $transaction->status;
        $transaction->update([
            'status' => 'approved_for_form_access',
            'admin_notes' => $data['admin_notes'] ?? null,
            'admin_approved_at' => now(),
            'last_updated_by' => $request->user()->id,
        ]);

        $this->logStatus($transaction, $request->user(), $fromStatus, 'approved_for_form_access', $data['admin_notes'] ?? 'Request approved for official form access.');
        $this->notifyStudent($transaction, 'Transaction approved', 'Your request has been approved. You may now open the official form and book your DOCUMATE appointment.', route('documate.transactions.show', $transaction));

        return back()->with('success', 'Transaction approved. The student can now access the official form.');
    }

    public function scheduleAppointment(Request $request, DocumateTransaction $transaction)
    {
        $user = $request->user();
        $this->authorizeTransaction($user, $transaction);
        abort_unless(in_array($transaction->status, ['approved_for_form_access', 'for_signatory', 'for_notary', 'appointment_scheduled'], true), 403);

        $data = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_session' => 'required|in:morning,afternoon',
            'remarks' => 'nullable|string|max:2000',
        ]);

        $appointmentDate = Carbon::parse($data['appointment_date'])->toDateString();
        $appointmentSession = $data['appointment_session'];
        $limits = config('documate.appointments');
        $sessionLimit = (int) data_get($limits, $appointmentSession, 25);
        $dailyLimit = (int) data_get($limits, 'daily', 50);

        $sessionCount = DocumateTransaction::query()
            ->whereDate('appointment_date', $appointmentDate)
            ->where('appointment_session', $appointmentSession)
            ->where('id', '!=', $transaction->id)
            ->whereNotIn('status', ['rejected'])
            ->count();

        if ($sessionCount >= $sessionLimit) {
            return back()->withErrors([
                'appointment_session' => 'The selected ' . $appointmentSession . ' schedule is already full. Please choose another session or date.',
            ]);
        }

        $dailyCount = DocumateTransaction::query()
            ->whereDate('appointment_date', $appointmentDate)
            ->where('id', '!=', $transaction->id)
            ->whereNotIn('status', ['rejected'])
            ->count();

        if ($dailyCount >= $dailyLimit) {
            return back()->withErrors([
                'appointment_date' => 'The selected date is already fully booked. Please choose another date.',
            ]);
        }

        $fromStatus = $transaction->status;
        $transaction->update([
            'appointment_date' => $appointmentDate,
            'appointment_session' => $appointmentSession,
            'appointment_booked_at' => now(),
            'status' => 'appointment_scheduled',
            'last_updated_by' => $user->id,
        ]);

        $appointmentLabel = $transaction->appointmentLabel();
        $remarks = $data['remarks'] ?: 'DOCUMATE appointment scheduled for ' . $appointmentLabel . '.';

        $this->logStatus($transaction, $user, $fromStatus, 'appointment_scheduled', $remarks);
        $this->notifyReviewers($transaction, 'Transaction appointment booked', $transaction->user?->name . ' booked a ' . strtolower((string) $appointmentLabel) . ' appointment.', route('documate.transactions.show', $transaction));
        $this->notifyStudent($transaction, 'Appointment confirmed', 'Your DOCUMATE appointment is scheduled for ' . $appointmentLabel . '.', route('documate.transactions.show', $transaction));

        return back()->with('success', 'Your DOCUMATE appointment has been scheduled successfully.');
    }

    public function updateStatus(Request $request, DocumateTransaction $transaction)
    {
        $user = $request->user();
        $isStudent = $transaction->user_id === $user->id;
        $isAdmin = $user->isDocumateAdmin();
        $isOfficer = $user->isStudentOfficer();
        $canManage = $isAdmin || $isOfficer;

        abort_unless($isStudent || $canManage, 403);

        $allowedStatuses = $isStudent
            ? ['for_signatory', 'for_notary']
            : ($isAdmin
                ? ['approved_for_form_access', 'under_review', 'completed', 'rejected']
                : ['under_review', 'completed', 'rejected']);

        $data = $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'remarks' => 'nullable|string|max:2000',
        ]);

        if ($isStudent && in_array($transaction->status, ['pending_admin_approval', 'rejected', 'completed'], true)) {
            return back()->withErrors(['status' => 'You cannot update this transaction at its current stage.']);
        }

        if ($isStudent && $transaction->status === 'appointment_scheduled') {
            return back()->withErrors(['status' => 'Your appointment is already scheduled. Use the appointment section if you need to change the date or session.']);
        }

        if ($isOfficer && $transaction->status === 'pending_admin_approval') {
            return back()->withErrors(['status' => 'Admin approval is required before the transaction can proceed.']);
        }

        if ($canManage && in_array($transaction->status, ['completed', 'rejected'], true)) {
            return back()->withErrors(['status' => 'Completed or rejected transactions can no longer be updated.']);
        }

        if ($canManage && $transaction->status === 'pending_admin_approval' && !in_array($data['status'], ['approved_for_form_access', 'rejected'], true)) {
            return back()->withErrors(['status' => 'Pending requests must be approved or rejected before further review stages.']);
        }

        if ($canManage && in_array($data['status'], ['under_review', 'completed'], true) && !$transaction->appointment_date) {
            return back()->withErrors(['status' => 'Schedule the student appointment first before moving this transaction forward.']);
        }

        if ($data['status'] === 'for_notary' && !$transaction->transactionType?->requires_notary) {
            return back()->withErrors(['status' => 'This transaction does not require notarization.']);
        }

        $fromStatus = $transaction->status;
        $transaction->status = $data['status'];
        $transaction->last_updated_by = $user->id;

        if ($data['status'] === 'completed') {
            $transaction->completed_at = now();
        }

        $transaction->save();

        $this->logStatus($transaction, $user, $fromStatus, $data['status'], $data['remarks'] ?? null);
        $this->notifyStudent(
            $transaction,
            'Transaction status updated',
            'Your transaction is now marked as ' . (config('documate.statuses.' . $data['status']) ?? $data['status']) . '.',
            route('documate.transactions.show', $transaction)
        );

        return back()->with('success', 'Transaction status updated successfully.');
    }

    public function export(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isDocumateAdmin() || $user->isStudentOfficer() || $user->isStudentUser(), 403);

        $format = $request->string('format', 'csv')->toString();
        $query = DocumateTransaction::query()->with('transactionType', 'user.studentProfile')->latest();

        if ($user->isStudentUser()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('transaction_type_id')) {
            $query->where('transaction_type_id', $request->integer('transaction_type_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', substr((string) $request->month, 5, 2))
                ->whereYear('created_at', substr((string) $request->month, 0, 4));
        }

        if (!$user->isStudentUser() && $request->filled('course')) {
            $query->whereHas('user.studentProfile', function ($builder) use ($request) {
                $builder->where('course', 'like', '%' . $request->string('course')->trim()->toString() . '%');
            });
        }

        $transactions = $this->sortTransactions($query->get(), $request);

        if ($format === 'json') {
            return response()->json($transactions);
        }

        $lines = [
            ['Reference', 'Student', 'Student Number', 'Course', 'Transaction', 'Status', 'Appointment Date', 'Appointment Session', 'Requested At', 'Completed At'],
        ];

        foreach ($transactions as $transaction) {
            $lines[] = [
                $transaction->reference_no,
                $transaction->user->name,
                $transaction->user->studentProfile?->student_number,
                $transaction->user->studentProfile?->course,
                $transaction->transactionType?->name,
                $transaction->status,
                optional($transaction->appointment_date)->toDateString(),
                $transaction->appointment_session,
                optional($transaction->requested_at)->toDateTimeString(),
                optional($transaction->completed_at)->toDateTimeString(),
            ];
        }

        $csv = collect($lines)->map(fn ($line) => collect($line)->map(function ($value) {
            $value = str_replace('"', '""', (string) $value);
            return '"' . $value . '"';
        })->implode(','))->implode("\n");

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="documate-transactions.csv"');
    }

    protected function authorizeTransaction($user, DocumateTransaction $transaction, bool $mustBeOwner = false): void
    {
        if ($mustBeOwner) {
            abort_unless($transaction->user_id === $user->id, 403);
            return;
        }

        abort_unless(
            $user->isDocumateAdmin()
            || $user->isStudentOfficer()
            || $transaction->user_id === $user->id,
            403
        );
    }

    protected function buildFormPayload($user, DocumateTransactionType $type): array
    {
        $profile = $user->studentProfile;

        return [
            'student_name' => $user->name,
            'student_email' => $user->email,
            'student_phone' => $user->phone,
            'student_number' => $profile?->student_number,
            'course' => $profile?->course,
            'college' => $profile?->college,
            'year_level' => $profile?->year_level,
            'section' => $profile?->section,
            'address' => $profile?->address,
            'guardian_name' => $profile?->guardian_name,
            'guardian_contact' => $profile?->guardian_contact,
            'transaction_code' => $type->code,
            'transaction_name' => $type->name,
        ];
    }

    protected function buildExampleTransaction(DocumateTransactionType $type): DocumateTransaction
    {
        $appointmentDate = now()->copy()->addDays(($type->sort_order % 5) + 2);
        $appointmentSession = ($type->sort_order % 2 === 0) ? 'afternoon' : 'morning';

        $student = new User([
            'name' => 'Angela Marie Torres',
            'first_name' => 'Angela',
            'middle_name' => 'Marie',
            'last_name' => 'Torres',
            'extension_name' => 'N/A',
            'has_no_middle_name' => false,
            'email' => 'angela.torres@example.edu.ph',
            'phone' => '09171234567',
        ]);

        $student->setRelation('studentProfile', new StudentProfile([
            'student_number' => '2026-01428',
            'course' => 'BS Information Technology',
            'college' => 'College of Computing',
            'year_level' => '3rd Year',
            'section' => '3A',
            'address' => 'Paterno Street, Tacloban City, Leyte',
            'guardian_name' => 'Roberto Torres',
            'guardian_contact' => '09181234567',
        ]));

        $transaction = new DocumateTransaction([
            'reference_no' => 'DOC-EX-' . $type->code,
            'status' => 'appointment_scheduled',
            'student_notes' => $this->buildExampleStudentNotes($type),
            'admin_notes' => 'Example preview approved for demonstration purposes only.',
            'requested_at' => now()->copy()->subDays(3)->setTime(9, 15),
            'admin_approved_at' => now()->copy()->subDays(2)->setTime(13, 30),
            'appointment_date' => $appointmentDate->toDateString(),
            'appointment_session' => $appointmentSession,
            'appointment_booked_at' => now()->copy()->subDay()->setTime(15, 0),
            'form_payload' => $this->buildExampleFormPayload($type),
        ]);

        $transaction->setRelation('user', $student);
        $transaction->setRelation('transactionType', $type);

        return $transaction;
    }

    protected function buildExampleFormPayload(DocumateTransactionType $type): array
    {
        return [
            'student_name' => 'Angela Marie Torres',
            'student_email' => 'angela.torres@example.edu.ph',
            'student_phone' => '09171234567',
            'student_number' => '2026-01428',
            'course' => 'BS Information Technology',
            'college' => 'College of Computing',
            'year_level' => '3rd Year',
            'section' => '3A',
            'address' => 'Paterno Street, Tacloban City, Leyte',
            'guardian_name' => 'Roberto Torres',
            'guardian_contact' => '09181234567',
            'transaction_code' => $type->code,
            'transaction_name' => $type->name,
        ];
    }

    protected function buildExampleStudentNotes(DocumateTransactionType $type): string
    {
        $signatoryCount = count($type->required_signatories ?? []);
        $signatorySummary = $signatoryCount === 0
            ? 'No manual signatories are listed in this sample workflow.'
            : 'This sample includes ' . $signatoryCount . ' required signator' . ($signatoryCount === 1 ? 'y' : 'ies') . '.';

        $notarySummary = $type->requires_notary
            ? ' Notarization is also required before the final DOCUMATE appointment.'
            : '';

        return 'Example preview only for ' . ($type->short_name ?? $type->name) . '. '
            . 'Use this sample to see how student information, routing instructions, and signature blocks appear on the printable form. '
            . $signatorySummary
            . $notarySummary;
    }

    protected function logStatus(DocumateTransaction $transaction, $actor, ?string $fromStatus, string $toStatus, ?string $remarks = null, ?string $filePath = null): void
    {
        DocumateTransactionStatusLog::create([
            'transaction_id' => $transaction->id,
            'actor_id' => $actor?->id,
            'actor_role' => $actor?->roles?->pluck('name')?->implode(', '),
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'remarks' => $remarks,
            'file_path' => $filePath,
        ]);
    }

    protected function notifyReviewers(DocumateTransaction $transaction, string $title, string $message, string $url): void
    {
        $reviewers = \App\Models\User::query()
            ->whereHas('roles', function ($builder) {
                $builder->whereIn('name', ['administrator', 'admin', 'student-officer']);
            })
            ->get();

        Notification::send($reviewers, new DocumateTransactionNotification($transaction, $title, $message, $url));
    }

    protected function notifyStudent(DocumateTransaction $transaction, string $title, string $message, string $url): void
    {
        $transaction->user?->notify(new DocumateTransactionNotification($transaction, $title, $message, $url));
    }

    protected function buildAppointmentAvailability(): Collection
    {
        $days = (int) data_get(config('documate.appointments'), 'lookahead_days', 10);
        $today = Carbon::today();
        $endDate = $today->copy()->addDays($days - 1);
        $limits = config('documate.appointments');
        $dailyLimit = (int) data_get($limits, 'daily', 50);
        $morningLimit = (int) data_get($limits, 'morning', 25);
        $afternoonLimit = (int) data_get($limits, 'afternoon', 25);

        $counts = DocumateTransaction::query()
            ->selectRaw('appointment_date, appointment_session, COUNT(*) as total')
            ->whereNotNull('appointment_date')
            ->whereBetween('appointment_date', [$today->toDateString(), $endDate->toDateString()])
            ->whereNotIn('status', ['rejected'])
            ->groupBy('appointment_date', 'appointment_session')
            ->get()
            ->groupBy(fn (DocumateTransaction $transaction) => optional($transaction->appointment_date)->toDateString());

        return collect(range(0, $days - 1))->map(function (int $offset) use ($today, $counts, $dailyLimit, $morningLimit, $afternoonLimit) {
            $date = $today->copy()->addDays($offset);
            $dateKey = $date->toDateString();
            $rows = collect($counts->get($dateKey, []));
            $morningCount = (int) optional($rows->firstWhere('appointment_session', 'morning'))->total;
            $afternoonCount = (int) optional($rows->firstWhere('appointment_session', 'afternoon'))->total;

            return [
                'date' => $date,
                'date_key' => $dateKey,
                'daily_remaining' => max($dailyLimit - ($morningCount + $afternoonCount), 0),
                'morning_remaining' => max($morningLimit - $morningCount, 0),
                'afternoon_remaining' => max($afternoonLimit - $afternoonCount, 0),
            ];
        });
    }

    protected function sortTransactions(Collection $transactions, Request $request): Collection
    {
        $sortBy = $request->string('sort_by', 'requested_at')->toString();
        $direction = strtolower($request->string('direction', 'desc')->toString()) === 'asc' ? 'asc' : 'desc';

        $sorted = match ($sortBy) {
            'transaction' => $transactions->sortBy(fn (DocumateTransaction $transaction) => $transaction->transactionType?->name ?? ''),
            'course' => $transactions->sortBy(fn (DocumateTransaction $transaction) => $transaction->user?->studentProfile?->course ?? ''),
            'month' => $transactions->sortBy(fn (DocumateTransaction $transaction) => optional($transaction->requested_at ?? $transaction->created_at)?->format('Y-m') ?? ''),
            'appointment' => $transactions->sortBy(fn (DocumateTransaction $transaction) => (($transaction->appointment_date?->toDateString() ?? '9999-12-31') . '-' . ($transaction->appointment_session ?? 'zz'))),
            'status' => $transactions->sortBy(fn (DocumateTransaction $transaction) => $transaction->status),
            default => $transactions->sortBy(fn (DocumateTransaction $transaction) => optional($transaction->requested_at ?? $transaction->created_at)?->timestamp ?? 0),
        };

        return $direction === 'desc' ? $sorted->reverse()->values() : $sorted->values();
    }
}
