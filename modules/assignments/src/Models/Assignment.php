<?php

namespace Moduloom\Assignments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Moduloom\Classrooms\Models\Classroom;

class Assignment extends Model
{
    protected $fillable = ['classroom_id', 'title', 'description', 'due_date'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
