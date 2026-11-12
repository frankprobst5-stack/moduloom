<?php

namespace Moduloom\Forums\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    protected $table = 'forum_threads';

    protected $fillable = ['title', 'author_name', 'body'];

    public function replies(): HasMany
    {
        return $this->hasMany(Reply::class, 'forum_thread_id');
    }
}
