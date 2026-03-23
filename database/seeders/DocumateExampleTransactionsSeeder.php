<?php

namespace Database\Seeders;

use App\Models\DocumateTransaction;
use App\Models\DocumateTransactionStatusLog;
use App\Models\DocumateTransactionType;
use App\Models\StudentProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DocumateExampleTransactionsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $officer = User::where('email', 'officer@example.com')->first();

        if (!$admin || !$officer) {
            return;
        }

        $exampleStudent = User::updateOrCreate(
            ['email' => 'examples@example.com'],
            [
                'name' => 'DOCUMATE Example Student',
                'phone' => '1234567893',
                'status' => 1,
                'email_verified_at' => now(),
                'password' => Hash::make('examples123'),
            ]
        );

        $exampleStudent->syncRoles(['student']);

        StudentProfile::updateOrCreate(
            ['user_id' => $exampleStudent->id],
            [
                'student_number' => '2026-EX-002',
                'course' => 'BS Office Administration',
                'college' => 'College of Management and Student Services',
                'year_level' => '4th Year',
                'section' => 'DOC-1',
                'address' => '123 Example Street, Demo City',
                'guardian_name' => 'Antonio Example',
                'guardian_contact' => '09123456781',
                'emergency_contact' => '09123456782',
                'clearance_status' => 'cleared',
                'clearance_notes' => 'Seeded example profile for transaction demonstrations.',
                'tagged_by' => $officer->id,
                'tagged_at' => now()->subDay(),
            ]
        );

        $transactionTypes = DocumateTransactionType::query()
            ->orderBy('sort_order')
            ->get();

        foreach ($transactionTypes as $index => $type) {
            $finalStatus = $this->resolveFinalStatus($type, $index);
            $timeline = $this->buildTimeline($type, $finalStatus, $index);
            $events = $timeline['events'];
            $timestamps = collect($events)->mapWithKeys(fn (array $event) => [$event['status'] => $event['at']]);

            $transaction = DocumateTransaction::withTrashed()->firstOrNew([
                'user_id' => $exampleStudent->id,
                'transaction_type_id' => $type->id,
            ]);

            $transaction->fill([
                'reference_no' => sprintf('DOC-EX-%03d', $type->sort_order ?: ($index + 1)),
                'status' => $finalStatus,
                'student_notes' => 'Example transaction for ' . $type->name . '. This seeded record demonstrates the '
                    . strtolower(config('documate.statuses.' . $finalStatus, str_replace('_', ' ', $finalStatus)))
                    . ' stage inside DOCUMATE.',
                'admin_notes' => $this->reviewerNoteForStatus($type->name, $finalStatus),
                'form_payload' => $this->buildFormPayload($exampleStudent, $type),
                'requested_at' => $timestamps->get('pending_admin_approval'),
                'admin_approved_at' => $timestamps->get('approved_for_form_access'),
                'appointment_date' => $timeline['appointment_date']?->toDateString(),
                'appointment_session' => $timeline['appointment_session'],
                'appointment_booked_at' => $timestamps->get('appointment_scheduled'),
                'completed_at' => $timestamps->get('completed'),
                'last_updated_by' => $this->actorForStatus($finalStatus, $exampleStudent, $admin, $officer)->id,
                'deleted_at' => null,
            ]);
            $transaction->save();

            DocumateTransactionStatusLog::where('transaction_id', $transaction->id)->delete();

            $previousStatus = null;

            foreach ($events as $event) {
                $actor = $this->actorForStatus($event['status'], $exampleStudent, $admin, $officer);

                DocumateTransactionStatusLog::create([
                    'transaction_id' => $transaction->id,
                    'actor_id' => $actor->id,
                    'actor_role' => $actor->roles->pluck('name')->implode(', '),
                    'from_status' => $previousStatus,
                    'to_status' => $event['status'],
                    'remarks' => $this->remarksForStatus(
                        $type->short_name ?: $type->name,
                        $event['status'],
                        $timeline['appointment_date'],
                        $timeline['appointment_session']
                    ),
                    'created_at' => $event['at'],
                    'updated_at' => $event['at'],
                ]);

                $previousStatus = $event['status'];
            }
        }
    }

    protected function resolveFinalStatus(DocumateTransactionType $type, int $index): string
    {
        if ($type->code === 'F-SDM-004') {
            return 'for_notary';
        }

        if ($type->code === 'F-SDM-013') {
            return 'completed';
        }

        $statuses = [
            'pending_admin_approval',
            'approved_for_form_access',
            'for_signatory',
            'appointment_scheduled',
            'under_review',
            'completed',
            'rejected',
        ];

        return $statuses[$index % count($statuses)];
    }

    protected function buildTimeline(DocumateTransactionType $type, string $finalStatus, int $index): array
    {
        $session = $index % 2 === 0 ? 'morning' : 'afternoon';
        $events = [];
        $appointmentDate = null;

        switch ($finalStatus) {
            case 'pending_admin_approval':
                $pending = now()->copy()->subHours(8 + $index);
                $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
                break;

            case 'approved_for_form_access':
                $pending = now()->copy()->subDays(1 + ($index % 2))->setTime(8 + ($index % 4), 15);
                $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
                $events[] = ['status' => 'approved_for_form_access', 'at' => $pending->copy()->addHours(5)];
                break;

            case 'for_signatory':
                $pending = now()->copy()->subDays(2 + ($index % 3))->setTime(8 + ($index % 3), 0);
                $approved = $pending->copy()->addHours(5);
                $signatory = $approved->copy()->addHours(18);

                $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
                $events[] = ['status' => 'approved_for_form_access', 'at' => $approved];
                $events[] = ['status' => 'for_signatory', 'at' => $signatory];
                break;

            case 'for_notary':
                $pending = now()->copy()->subDays(3 + ($index % 2))->setTime(8, 30);
                $approved = $pending->copy()->addHours(5);
                $signatory = $approved->copy()->addHours(16);
                $notary = $signatory->copy()->addHours(20);

                $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
                $events[] = ['status' => 'approved_for_form_access', 'at' => $approved];
                $events[] = ['status' => 'for_signatory', 'at' => $signatory];
                $events[] = ['status' => 'for_notary', 'at' => $notary];
                break;

            case 'appointment_scheduled':
                $appointmentDate = now()->copy()->addDays(1 + ($index % 5));
                $events = $this->timelineWithAppointment($type, $appointmentDate, $session, false, false);
                break;

            case 'under_review':
                $appointmentDate = now()->copy()->subDays(1 + ($index % 2));
                $events = $this->timelineWithAppointment($type, $appointmentDate, $session, true, false);
                break;

            case 'completed':
                $appointmentDate = now()->copy()->subDays(4 + ($index % 6));
                $events = $this->timelineWithAppointment($type, $appointmentDate, $session, true, true);
                break;

            case 'rejected':
                $pending = now()->copy()->subDays(2 + ($index % 4))->setTime(9, 0);
                $approved = $pending->copy()->addHours(4);
                $rejected = $approved->copy()->addHours(8);

                $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
                $events[] = ['status' => 'approved_for_form_access', 'at' => $approved];
                $events[] = ['status' => 'rejected', 'at' => $rejected];
                break;
        }

        return [
            'events' => $events,
            'appointment_date' => $appointmentDate,
            'appointment_session' => $appointmentDate ? $session : null,
        ];
    }

    protected function timelineWithAppointment(
        DocumateTransactionType $type,
        Carbon $appointmentDate,
        string $session,
        bool $includeReview,
        bool $includeCompletion
    ): array {
        $events = [];

        $pending = $appointmentDate->copy()->subDays($type->requires_notary ? 5 : 4)->setTime(8, 15);
        $approved = $pending->copy()->addHours(5);
        $signatory = $approved->copy()->addHours(20);

        $events[] = ['status' => 'pending_admin_approval', 'at' => $pending];
        $events[] = ['status' => 'approved_for_form_access', 'at' => $approved];
        $events[] = ['status' => 'for_signatory', 'at' => $signatory];

        if ($type->requires_notary) {
            $notary = $signatory->copy()->addHours(18);
            $events[] = ['status' => 'for_notary', 'at' => $notary];
        }

        $bookedAt = $appointmentDate->copy()->subDays(1)->setTime(14, 0);
        $events[] = ['status' => 'appointment_scheduled', 'at' => $bookedAt];

        if ($includeReview) {
            $reviewAt = $appointmentDate->copy()->setTime($session === 'morning' ? 10 : 15, 30);
            $events[] = ['status' => 'under_review', 'at' => $reviewAt];

            if ($includeCompletion) {
                $events[] = ['status' => 'completed', 'at' => $reviewAt->copy()->addHours(6)];
            }
        }

        return $events;
    }

    protected function buildFormPayload(User $student, DocumateTransactionType $type): array
    {
        $profile = $student->studentProfile;

        return [
            'student_name' => $student->name,
            'student_email' => $student->email,
            'student_phone' => $student->phone,
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

    protected function reviewerNoteForStatus(string $typeName, string $status): ?string
    {
        return match ($status) {
            'pending_admin_approval' => null,
            'approved_for_form_access' => 'Example approval note for ' . $typeName . '. The official form is now available.',
            'for_signatory' => 'Example reviewer note for ' . $typeName . '. The student is currently collecting signatures.',
            'for_notary' => 'Example reviewer note for ' . $typeName . '. Notarization is the current required step.',
            'appointment_scheduled' => 'Example reviewer note for ' . $typeName . '. The student already has a confirmed office schedule.',
            'under_review' => 'Example reviewer note for ' . $typeName . '. The office already received the accomplished form.',
            'completed' => 'Example reviewer note for ' . $typeName . '. This record is already completed for demonstration purposes.',
            'rejected' => 'Example reviewer note for ' . $typeName . '. The request was rejected for demonstration purposes.',
            default => null,
        };
    }

    protected function actorForStatus(string $status, User $student, User $admin, User $officer): User
    {
        return match ($status) {
            'pending_admin_approval',
            'for_signatory',
            'for_notary',
            'appointment_scheduled' => $student,
            'approved_for_form_access',
            'rejected' => $admin,
            'under_review',
            'completed' => $officer,
            default => $admin,
        };
    }

    protected function remarksForStatus(
        string $typeName,
        string $status,
        ?Carbon $appointmentDate = null,
        ?string $appointmentSession = null
    ): string {
        return match ($status) {
            'pending_admin_approval' => 'Example request submitted for ' . $typeName . '.',
            'approved_for_form_access' => 'Example administrator approval granted for ' . $typeName . '.',
            'for_signatory' => 'Example student update: ' . $typeName . ' is being routed to the required signatories.',
            'for_notary' => 'Example student update: ' . $typeName . ' is now with the notary for final legal acknowledgment.',
            'appointment_scheduled' => 'Example appointment booked for '
                . optional($appointmentDate)->format('M d, Y')
                . ' - '
                . ucfirst((string) $appointmentSession)
                . '.',
            'under_review' => 'Example reviewer update: ' . $typeName . ' is now under office review.',
            'completed' => 'Example reviewer update: ' . $typeName . ' has been completed.',
            'rejected' => 'Example reviewer update: ' . $typeName . ' was rejected after validation.',
            default => 'Example status update for ' . $typeName . '.',
        };
    }
}
