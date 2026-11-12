<?php

namespace Moduloom\Tasks\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'done', 'due_date'];

    protected function casts(): array
    {
        return [
            'done' => 'boolean',
            'due_date' => 'date',
        ];
    }
}
