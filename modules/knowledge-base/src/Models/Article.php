<?php

namespace Moduloom\KnowledgeBase\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'kb_articles';

    protected $fillable = ['title', 'slug', 'category', 'content'];
}
