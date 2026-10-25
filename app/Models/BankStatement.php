<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankStatement extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_name',
        'period_start',
        'period_end',
        'opening_balance',
        'closing_balance',
        'status',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_balance' => 'integer',
        'closing_balance' => 'integer',
    ];

    public function entries(): HasMany
    {
        return $this->hasMany(BankStatementEntry::class);
    }
}
