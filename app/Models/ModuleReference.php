<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleReference extends Model
{
    protected $fillable = ['module_slug', 'reference_type', 'reference_identifier'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(InstalledModule::class, 'module_slug', 'module_slug');
    }
}
