<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectorAdvanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'director_advance_id',
        'expense_date',
        'category_id',
        'description',
        'amount',
        'project_id',
        'receipt_path',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function advance(): BelongsTo
    {
        return $this->belongsTo(DirectorAdvance::class, 'director_advance_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
