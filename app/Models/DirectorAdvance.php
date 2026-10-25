<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DirectorAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'person_name',
        'advance_date',
        'amount',
        'reimbursed_amount',
        'outstanding_amount',
        'purpose',
        'project_id',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'advance_date' => 'date',
        'amount' => 'integer',
        'reimbursed_amount' => 'integer',
        'outstanding_amount' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DirectorAdvanceItem::class);
    }

    public function reimbursements(): HasMany
    {
        return $this->hasMany(DirectorReimbursement::class);
    }
}
