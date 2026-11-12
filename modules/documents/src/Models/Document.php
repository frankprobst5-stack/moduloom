<?php

namespace Moduloom\Documents\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = ['title', 'disk_path', 'mime_type', 'size_bytes'];

    public function url(): string
    {
        return Storage::disk('public')->url($this->disk_path);
    }
}
