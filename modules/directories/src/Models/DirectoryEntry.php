<?php

namespace Moduloom\Directories\Models;

use Illuminate\Database\Eloquent\Model;

class DirectoryEntry extends Model
{
    protected $fillable = ['name', 'category', 'description', 'contact_info'];
}
