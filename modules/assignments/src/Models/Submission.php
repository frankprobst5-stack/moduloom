<?php

namespace Moduloom\Assignments\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'assignment_submissions';

    protected $fillable = ['assignment_id', 'student_name', 'content', 'submitted_at'];

    protected function casts(): array
    {
        return ['submitted_at' => 'datetime'];
    }
}
