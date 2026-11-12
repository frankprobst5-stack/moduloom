<?php

namespace Moduloom\PersonalDatabases\Models;

use Illuminate\Database\Eloquent\Model;

class PdRecord extends Model
{
    protected $fillable = ['pd_collection_id', 'data'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
