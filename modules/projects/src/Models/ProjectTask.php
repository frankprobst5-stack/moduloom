<?php

namespace Moduloom\Projects\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    protected $fillable = ['project_id', 'title', 'done', 'assignee_name'];

    protected function casts(): array
    {
        return ['done' => 'boolean'];
    }
}
