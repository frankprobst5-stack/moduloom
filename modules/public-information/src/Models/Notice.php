<?php

namespace Moduloom\PublicInformation\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $table = 'public_notices';

    protected $fillable = ['title', 'body', 'published_at'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }
}
