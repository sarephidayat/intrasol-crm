<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectorReimbursement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'director_advance_id',
        'cash_transaction_id',
        'payment_date',
        'amount',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'integer',
    ];

    public function advance(): BelongsTo
    {
        return $this->belongsTo(DirectorAdvance::class, 'director_advance_id');
    }

    public function cashTransaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class);
    }
}
