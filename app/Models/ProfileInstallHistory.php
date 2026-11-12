<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileInstallHistory extends Model
{
    protected $table = 'profile_install_history';

    protected $fillable = ['profile_slug', 'version', 'installed_by', 'installed_at'];

    protected function casts(): array
    {
        return [
            'installed_at' => 'datetime',
        ];
    }
}
