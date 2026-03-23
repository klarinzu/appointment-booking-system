<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumateTransactionStatusLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(DocumateTransaction::class, 'transaction_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
