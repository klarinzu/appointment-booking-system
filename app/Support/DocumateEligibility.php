<?php

namespace App\Support;

use App\Models\DocumateTransaction;
use App\Models\DocumateTransactionType;
use App\Models\User;

class DocumateEligibility
{
    public function evaluate(User $user, DocumateTransactionType $type): array
    {
        $reasons = [];
        $profile = $user->studentProfile;

        if (!$profile) {
            $reasons[] = 'Complete your student profile before requesting a transaction.';
        } else {
            foreach (($type->required_profile_fields ?? []) as $field) {
                if (!filled($profile->{$field})) {
                    $label = str_replace('_', ' ', $field);
                    $reasons[] = 'Missing required student profile field: ' . ucfirst($label) . '.';
                }
            }

            if ($profile->clearance_status === 'hold' && !str_contains($type->slug, 'clearance')) {
                $reasons[] = 'Your account has a clearance hold. Please contact the student officer or VPSD first.';
            }
        }

        $hasOpenRequest = DocumateTransaction::query()
            ->where('user_id', $user->id)
            ->where('transaction_type_id', $type->id)
            ->whereIn('status', DocumateTransaction::OPEN_STATUSES)
            ->exists();

        if ($hasOpenRequest) {
            $reasons[] = 'You already have an ongoing request for this transaction.';
        }

        if (!$type->is_active) {
            $reasons[] = 'This transaction is currently unavailable.';
        }

        return [
            'eligible' => count($reasons) === 0,
            'reasons' => $reasons,
        ];
    }
}
