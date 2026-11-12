<?php

namespace Moduloom\Applications\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    protected $fillable = ['applicant_name', 'email', 'application_type', 'status', 'notes'];
}
