<?php

namespace Moduloom\Forums\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'forum_replies';

    protected $fillable = ['forum_thread_id', 'author_name', 'body'];
}
