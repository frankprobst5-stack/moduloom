<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleCapabilityGrant extends Model
{
    protected $fillable = ['role_slug', 'capability_slug', 'granted_by_source'];
}
