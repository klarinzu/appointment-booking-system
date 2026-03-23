<?php

use App\Models\DocumateTransaction;
use App\Models\DocumateTransactionType;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the scheduled transaction calendar as a separate page', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();

    $dashboardResponse = $this->actingAs($student)->get(route('dashboard'));

    $dashboardResponse->assertOk();
    $dashboardResponse->assertDontSee('Scheduled Transaction Calendar');
    $dashboardResponse->assertSee('Scheduled Calendar');

    $response = $this->actingAs($student)->get(route('documate.calendar.index'));

    $response->assertOk();
    $response->assertSee('Scheduled Transaction Calendar');
    $response->assertSee('Monthly Appointment Load');
    $response->assertSee('Auto-refresh every 30 seconds');
});

it('returns grouped booked transaction counts for admins on the dashboard calendar feed', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::where('email', 'admin@example.com')->firstOrFail();
    $student = User::where('email', 'student@example.com')->firstOrFail();
    $type = DocumateTransactionType::firstOrFail();
    $month = now()->addMonth()->startOfMonth();
    $targetDate = $month->copy()->addDays(4)->toDateString();

    DocumateTransaction::create([
        'user_id' => $student->id,
        'transaction_type_id' => $type->id,
        'reference_no' => 'DOC-CAL-ADMIN-1',
        'status' => 'appointment_scheduled',
        'appointment_date' => $targetDate,
        'appointment_session' => 'morning',
        'requested_at' => now(),
    ]);

    DocumateTransaction::create([
        'user_id' => $student->id,
        'transaction_type_id' => $type->id,
        'reference_no' => 'DOC-CAL-ADMIN-2',
        'status' => 'under_review',
        'appointment_date' => $targetDate,
        'appointment_session' => 'afternoon',
        'requested_at' => now(),
    ]);

    DocumateTransaction::create([
        'user_id' => $student->id,
        'transaction_type_id' => $type->id,
        'reference_no' => 'DOC-CAL-ADMIN-3',
        'status' => 'rejected',
        'appointment_date' => $targetDate,
        'appointment_session' => 'afternoon',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($admin)->getJson(route('dashboard.transaction-calendar', [
        'month' => $month->format('Y-m'),
    ]));

    $response->assertOk();
    $response->assertJsonPath('summary.total_booked', 2);
    $response->assertJsonPath('summary.active_scheduled', 1);
    $response->assertJsonFragment([
        'date' => $targetDate,
        'total' => 2,
        'morning' => 1,
        'afternoon' => 1,
        'active_scheduled' => 1,
        'under_review' => 1,
        'completed' => 0,
    ]);
});

it('limits the dashboard calendar feed to the current student for student dashboards', function () {
    $this->seed(DatabaseSeeder::class);

    $student = User::where('email', 'student@example.com')->firstOrFail();
    $type = DocumateTransactionType::firstOrFail();
    $otherStudent = User::factory()->create([
        'email' => 'other-student@example.com',
    ]);
    $otherStudent->assignRole('student');
    $month = now()->addMonth()->startOfMonth();
    $targetDate = $month->copy()->addDays(9)->toDateString();

    DocumateTransaction::create([
        'user_id' => $student->id,
        'transaction_type_id' => $type->id,
        'reference_no' => 'DOC-CAL-STUDENT-1',
        'status' => 'appointment_scheduled',
        'appointment_date' => $targetDate,
        'appointment_session' => 'morning',
        'requested_at' => now(),
    ]);

    DocumateTransaction::create([
        'user_id' => $otherStudent->id,
        'transaction_type_id' => $type->id,
        'reference_no' => 'DOC-CAL-STUDENT-2',
        'status' => 'appointment_scheduled',
        'appointment_date' => $targetDate,
        'appointment_session' => 'afternoon',
        'requested_at' => now(),
    ]);

    $response = $this->actingAs($student)->getJson(route('dashboard.transaction-calendar', [
        'month' => $month->format('Y-m'),
    ]));

    $response->assertOk();
    $response->assertJsonPath('summary.total_booked', 1);
    $response->assertJsonFragment([
        'date' => $targetDate,
        'total' => 1,
        'morning' => 1,
        'afternoon' => 0,
    ]);
});
