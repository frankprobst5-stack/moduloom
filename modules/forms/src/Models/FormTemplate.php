<?php

namespace Moduloom\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormTemplate extends Model
{
    protected $fillable = ['title', 'description'];

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
