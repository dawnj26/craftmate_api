<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    use HasFactory;

    protected $fillables = [
        'project_id',
        'parent_id',
        'step_number',
        'content',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Step::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Step::class, 'parent_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
