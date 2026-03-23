<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumateTransaction extends Model
{
    use SoftDeletes;

    public const OPEN_STATUSES = [
        'pending_admin_approval',
        'approved_for_form_access',
        'for_signatory',
        'for_notary',
        'appointment_scheduled',
        'under_review',
    ];

    protected $guarded = [];

    protected $casts = [
        'form_payload' => 'array',
        'requested_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'appointment_date' => 'date',
        'appointment_booked_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(DocumateTransactionType::class, 'transaction_type_id');
    }

    public function updates()
    {
        return $this->hasMany(DocumateTransactionStatusLog::class, 'transaction_id')->latest();
    }

    public function latestUpdate()
    {
        return $this->hasOne(DocumateTransactionStatusLog::class, 'transaction_id')->latestOfMany();
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function appointmentLabel(): ?string
    {
        if (!$this->appointment_date || !$this->appointment_session) {
            return null;
        }

        $sessionLabel = match ($this->appointment_session) {
            'morning' => 'Morning',
            'afternoon' => 'Afternoon',
            default => ucfirst((string) $this->appointment_session),
        };

        return $this->appointment_date->format('M d, Y') . ' - ' . $sessionLabel;
    }
}
