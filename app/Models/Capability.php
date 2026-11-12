<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Capability extends Model
{
    protected $primaryKey = 'capability_slug';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['capability_slug', 'label'];
}
