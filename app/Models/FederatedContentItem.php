<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FederatedContentItem extends Model
{
    protected $table = 'federated_content';

    protected $fillable = [
        'federation_peer_id',
        'content_type',
        'title',
        'slug',
        'content',
        'canonical_url',
        'published_at',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'fetched_at' => 'datetime',
        ];
    }

    public function peer(): BelongsTo
    {
        return $this->belongsTo(FederationPeer::class, 'federation_peer_id');
    }
}
