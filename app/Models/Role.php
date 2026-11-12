<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $primaryKey = 'role_slug';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['role_slug', 'label'];

    public function grants(): HasMany
    {
        return $this->hasMany(RoleCapabilityGrant::class, 'role_slug', 'role_slug');
    }
}
