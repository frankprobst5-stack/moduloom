<?php

namespace Moduloom\Records\Models;

use Illuminate\Database\Eloquent\Model;

class PublicRecord extends Model
{
    protected $table = 'public_records';

    protected $fillable = ['title', 'record_type', 'body', 'is_redacted', 'filed_at'];

    protected function casts(): array
    {
        return ['is_redacted' => 'boolean', 'filed_at' => 'date'];
    }
}
