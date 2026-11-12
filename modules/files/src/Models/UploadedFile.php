<?php

namespace Moduloom\Files\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UploadedFile extends Model
{
    protected $fillable = ['original_name', 'disk_path', 'mime_type', 'size_bytes'];

    public function url(): string
    {
        return Storage::disk('public')->url($this->disk_path);
    }
}
