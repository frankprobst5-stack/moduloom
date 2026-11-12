<?php

namespace Moduloom\Website\Models;

use Illuminate\Database\Eloquent\Model;

class WebsitePage extends Model
{
    protected $fillable = ['title', 'slug', 'content'];
}
