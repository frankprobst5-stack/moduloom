<?php

namespace Moduloom\Classrooms\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'classroom_students';

    protected $fillable = ['classroom_id', 'student_name'];
}
