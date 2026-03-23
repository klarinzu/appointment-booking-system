<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\DocumateTransaction;
use App\Models\DocumateTransactionType;
use App\Models\StudentProfile;
use App\Support\DocumateEligibility;
use App\Events\StatusUpdated;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $eligibility = app(DocumateEligibility::class);

        $recentTransactions = $user->isStudentUser()
            ? $user->documateTransactions()->with('transactionType')->latest()->take(5)->get()
            : DocumateTransaction::with('transactionType', 'user.studentProfile')->latest()->take(8)->get();

        $dashboardData = [
            'role_label' => $user->isDocumateAdmin()
                ? 'Administrator'
                : ($user->isStudentOfficer() ? 'Student Officer' : 'Student'),
            'cards' => [],
        ];

        if ($user->isStudentUser()) {
            $availableTransactions = DocumateTransactionType::where('is_active', true)->orderBy('sort_order')->get();
            $eligibleCount = $availableTransactions->filter(fn ($type) => $eligibility->evaluate($user, $type)['eligible'])->count();

            $dashboardData['cards'] = [
                ['label' => 'Available Transactions', 'value' => $eligibleCount, 'icon' => 'fas fa-file-signature'],
                ['label' => 'Pending Approval', 'value' => $user->documateTransactions()->where('status', 'pending_admin_approval')->count(), 'icon' => 'fas fa-user-clock'],
                ['label' => 'Scheduled Appointments', 'value' => $user->documateTransactions()->where('status', 'appointment_scheduled')->count(), 'icon' => 'fas fa-calendar-alt'],
                ['label' => 'Completed Requests', 'value' => $user->documateTransactions()->where('status', 'completed')->count(), 'icon' => 'fas fa-check-circle'],
            ];
        } else {
            $dashboardData['cards'] = [
                ['label' => 'Pending Approval', 'value' => DocumateTransaction::where('status', 'pending_admin_approval')->count(), 'icon' => 'fas fa-user-check'],
                ['label' => 'Appointments Booked', 'value' => DocumateTransaction::where('status', 'appointment_scheduled')->count(), 'icon' => 'fas fa-calendar-check'],
                ['label' => 'Completed This Month', 'value' => DocumateTransaction::where('status', 'completed')->whereMonth('updated_at', now()->month)->count(), 'icon' => 'fas fa-flag-checkered'],
                ['label' => 'Students Tagged', 'value' => StudentProfile::whereNotNull('tagged_at')->count(), 'icon' => 'fas fa-user-tag'],
            ];
        }

        return view('backend.dashboard.index', [
            'dashboardData' => $dashboardData,
            'recentTransactions' => $recentTransactions,
            'notifications' => $user->notifications()->latest()->take(5)->get(),
            'officeLocations' => config('documate.office_locations'),
            'statusLabels' => config('documate.statuses'),
        ]);
    }

    public function calendar()
    {
        $user = auth()->user();

        return view('backend.calendar.index', [
            'calendarMonthKey' => now()->startOfMonth()->format('Y-m'),
            'calendarFeedUrl' => route('dashboard.transaction-calendar'),
            'calendarRefreshSeconds' => 30,
            'calendarScopeLabel' => $user->isStudentUser()
                ? 'This page shows only your own scheduled DOCUMATE transactions.'
                : 'This page shows the live scheduled DOCUMATE transactions across the system.',
        ]);
    }

    public function transactionCalendar(Request $request)
    {
        $user = $request->user();
        $monthInput = $request->string('month')->toString();
        $month = preg_match('/^\d{4}-\d{2}$/', $monthInput)
            ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
            : now()->startOfMonth();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $dailyLimit = (int) data_get(config('documate.appointments'), 'daily', 50);

        $query = DocumateTransaction::query()
            ->whereNotNull('appointment_date')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->whereNotIn('status', ['rejected']);

        if ($user->isStudentUser()) {
            $query->where('user_id', $user->id);
        }

        $transactions = $query->get([
            'appointment_date',
            'appointment_session',
            'status',
        ]);

        $grouped = $transactions->groupBy(fn (DocumateTransaction $transaction) => optional($transaction->appointment_date)->toDateString());

        $days = $grouped->map(function ($items, $date) use ($dailyLimit) {
            $morning = $items->where('appointment_session', 'morning')->count();
            $afternoon = $items->where('appointment_session', 'afternoon')->count();
            $activeScheduled = $items->where('status', 'appointment_scheduled')->count();
            $underReview = $items->where('status', 'under_review')->count();
            $completed = $items->where('status', 'completed')->count();
            $total = $items->count();

            return [
                'date' => $date,
                'total' => $total,
                'morning' => $morning,
                'afternoon' => $afternoon,
                'active_scheduled' => $activeScheduled,
                'under_review' => $underReview,
                'completed' => $completed,
                'remaining' => max($dailyLimit - $total, 0),
                'is_full' => $total >= $dailyLimit,
            ];
        })->sortKeys()->values();

        $todayKey = now()->toDateString();
        $todaySummary = $days->firstWhere('date', $todayKey);
        $busiestDay = $days->sortByDesc('total')->first();

        return response()->json([
            'month' => $start->format('Y-m'),
            'month_label' => $start->format('F Y'),
            'generated_at' => now()->toIso8601String(),
            'summary' => [
                'total_booked' => $days->sum('total'),
                'active_scheduled' => $days->sum('active_scheduled'),
                'today_total' => data_get($todaySummary, 'total', 0),
                'busiest_day' => $busiestDay ? [
                    'date' => $busiestDay['date'],
                    'total' => $busiestDay['total'],
                ] : null,
            ],
            'days' => $days,
        ]);
    }

    // Helper function to get color based on status
    private function getStatusColor($status)
    {
        $colors = [
            'Pending payment' => '#f39c12',
            'Processing' => '#3498db',
            'Confirmed' => '#2ecc71',
            'Cancelled' => '#ff0000',
            'Completed' => '#008000',
            'On Hold' => '#95a5a6',
            'Rescheduled' => '#f1c40f',
            'No Show' => '#e67e22',
        ];

        return $colors[$status] ?? '#7f8c8d';
    }


    // In AppointmentController.php
    public function updateStatus(Request $request)
    {
        $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'status' => 'required|in:Pending payment,Processing,Confirmed,Cancelled,Completed,On Hold,No Show'
        ]);

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->status = $request->status;
        $appointment->save();

        event(new StatusUpdated($appointment));

        return back()->with('success', 'Status updated successfully');
    }
}
