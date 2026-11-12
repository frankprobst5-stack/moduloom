<?php

namespace Moduloom\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'starts_at', 'location'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime'];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}
