<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumateTransactionType extends Model
{
    protected $guarded = [];

    protected $casts = [
        'required_signatories' => 'array',
        'workflow_steps' => 'array',
        'required_profile_fields' => 'array',
        'is_active' => 'boolean',
        'requires_notary' => 'boolean',
        'admin_approval_required' => 'boolean',
        'allow_student_status_updates' => 'boolean',
        'requires_clearance' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(DocumateTransaction::class, 'transaction_type_id');
    }
}
