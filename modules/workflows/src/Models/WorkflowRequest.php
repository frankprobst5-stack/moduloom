<?php

namespace Moduloom\Workflows\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowRequest extends Model
{
    protected $fillable = ['title', 'description', 'requested_by', 'status'];
}
