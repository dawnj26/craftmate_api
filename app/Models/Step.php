<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'content',
        'order',
    ];

    protected $touches = ['project'];

    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'content' => 'array',
    ];

    public function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
