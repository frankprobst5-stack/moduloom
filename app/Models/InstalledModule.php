<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstalledModule extends Model
{
    protected $primaryKey = 'module_slug';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['module_slug', 'version', 'status', 'installed_at'];

    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
        ];
    }

    public function references(): HasMany
    {
        return $this->hasMany(ModuleReference::class, 'module_slug', 'module_slug');
    }
}
