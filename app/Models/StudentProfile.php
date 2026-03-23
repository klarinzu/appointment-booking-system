<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $guarded = [];

    protected $casts = [
        'birthdate' => 'date',
        'tagged_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taggedBy()
    {
        return $this->belongsTo(User::class, 'tagged_by');
    }

    public function hasRequiredProfileData(): bool
    {
        $required = [
            'student_number',
            'course',
            'college',
            'year_level',
            'address',
            'guardian_name',
            'guardian_contact',
        ];

        foreach ($required as $field) {
            if (!filled($this->{$field})) {
                return false;
            }
        }

        return true;
    }
}
