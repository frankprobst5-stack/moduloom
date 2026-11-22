<?php

namespace App\Kernel;

use App\Models\FederatedContentItem;
use App\Models\FederationPeer;
use Illuminate\Support\Facades\Http;

/**
 * Fed-b's inbound half: pulls each peer's subscribed feed(s) — the
 * exact envelope FederationFeed produces on the outbound side — and
 * upserts into federated_content. Pull only, on our own schedule;
 * never push, never a central directory. A peer that's unreachable or
 * returns something unexpected is skipped, not fatal — one bad peer
 * must never block fetching from the rest.
 */
class FederationFetcher
{
    public function fetchPeer(FederationPeer $peer): int
    {
        $count = 0;

        foreach ($peer->subscribed_content_types as $contentType) {
            $response = Http::timeout(10)->get(
                rtrim($peer->base_url, '/')."/federation/{$contentType}.json",
            );

            if (! $response->ok()) {
                continue;
            }

            foreach ($response->json('items', []) as $item) {
                if (empty($item['canonical_url']) || empty($item['title'])) {
                    continue;
                }

                FederatedContentItem::updateOrCreate(
                    [
                        'federation_peer_id' => $peer->id,
                        'canonical_url' => $item['canonical_url'],
                    ],
                    [
                        'content_type' => $contentType,
                        'title' => $item['title'],
                        'slug' => $item['slug'] ?? null,
                        'content' => $item['content'] ?? null,
                        'published_at' => $item['published_at'] ?? null,
                        'fetched_at' => now(),
                    ],
                );

                $count++;
            }
        }

        $peer->update(['last_fetched_at' => now()]);

        return $count;
    }

    public function fetchAll(): int
    {
        return FederationPeer::all()->sum(fn (FederationPeer $peer) => $this->fetchPeer($peer));
    }
}
