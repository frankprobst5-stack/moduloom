<?php

namespace Moduloom\Forms\Models;

use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    protected $fillable = ['form_template_id', 'data', 'submitted_at'];

    protected function casts(): array
    {
        return ['data' => 'array', 'submitted_at' => 'datetime'];
    }
}
