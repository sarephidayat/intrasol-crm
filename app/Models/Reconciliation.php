<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_statement_id',
        'reconciliation_date',
        'system_balance',
        'actual_bank_balance',
        'difference',
        'notes',
        'status',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'reconciliation_date' => 'date',
        'system_balance' => 'integer',
        'actual_bank_balance' => 'integer',
        'difference' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function bankStatement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
