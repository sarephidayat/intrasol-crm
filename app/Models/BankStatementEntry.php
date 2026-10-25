<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_statement_id',
        'entry_date',
        'description',
        'reference',
        'amount',
        'type',
        'status',
        'cash_transaction_id',
        'matched_by',
        'matched_at',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'integer',
        'matched_at' => 'datetime',
    ];

    public function statement(): BelongsTo
    {
        return $this->belongsTo(BankStatement::class, 'bank_statement_id');
    }

    public function cashTransaction(): BelongsTo
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function matcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }
}
