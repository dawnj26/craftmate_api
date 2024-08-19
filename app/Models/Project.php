<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillables = [
        'user_id',
        'parent_id',
        'title',
        'description',
        'is_public',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }
}
