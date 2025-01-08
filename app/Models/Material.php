<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'image', 'quantity', 'category_id', 'user_id'];

    public function decrementQuantity($amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Amount must be positive");
        }

        if ($this->quantity < $amount) {
            throw new \InvalidArgumentException("Insufficient quantity available for {$this->name}");
        }

        $this->decrement('quantity', $amount);
        $this->refresh();

        return $this;
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('material_quantity');
    }
    public function startedProjects() {
        return $this->belongsToMany(Project::class, 'started_project_material', 'material_id', 'project_id');
    }
}
