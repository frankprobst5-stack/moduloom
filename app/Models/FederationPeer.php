<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FederationPeer extends Model
{
    protected $fillable = ['base_url', 'label', 'subscribed_content_types', 'last_fetched_at'];

    protected function casts(): array
    {
        return [
            'subscribed_content_types' => 'array',
            'last_fetched_at' => 'datetime',
        ];
    }

    public function content(): HasMany
    {
        return $this->hasMany(FederatedContentItem::class);
    }
}
