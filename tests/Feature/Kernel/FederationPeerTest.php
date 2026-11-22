<?php

namespace Tests\Feature\Kernel;

use App\Kernel\FederationFetcher;
use App\Models\FederatedContentItem;
use App\Models\FederationPeer;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Fed-b: the inbound half. A peer is just an HTTP boundary — faked
 * here with Http::fake() rather than standing up a second real
 * instance, the standard way to test an outbound HTTP client
 * deterministically. The pipeline itself (fetch, parse, upsert,
 * idempotency) is exercised for real against that faked response.
 */
class FederationPeerTest extends TestCase
{
    public function test_fetching_a_peer_caches_its_feed_items(): void
    {
        Http::fake([
            'peer.example/federation/website.json' => Http::response([
                'instance' => ['name' => 'Peer Instance', 'url' => 'https://peer.example'],
                'content_type' => 'website',
                'items' => [
                    ['title' => 'About Peer', 'slug' => 'about', 'content' => 'Hello from the peer.', 'published_at' => '2026-01-01T00:00:00+00:00', 'canonical_url' => 'https://peer.example/site/about'],
                ],
            ]),
        ]);

        $peer = FederationPeer::create([
            'base_url' => 'https://peer.example',
            'label' => 'Peer Instance',
            'subscribed_content_types' => ['website'],
        ]);

        $count = app(FederationFetcher::class)->fetchPeer($peer);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('federated_content', [
            'federation_peer_id' => $peer->id,
            'title' => 'About Peer',
            'canonical_url' => 'https://peer.example/site/about',
        ]);
        $this->assertNotNull($peer->fresh()->last_fetched_at);
    }

    public function test_fetching_the_same_peer_twice_does_not_duplicate_items(): void
    {
        Http::fake([
            'peer.example/federation/website.json' => Http::response([
                'instance' => ['name' => 'Peer', 'url' => 'https://peer.example'],
                'content_type' => 'website',
                'items' => [
                    ['title' => 'About Peer', 'slug' => 'about', 'content' => 'v1', 'published_at' => null, 'canonical_url' => 'https://peer.example/site/about'],
                ],
            ]),
        ]);

        $peer = FederationPeer::create([
            'base_url' => 'https://peer.example',
            'label' => 'Peer',
            'subscribed_content_types' => ['website'],
        ]);

        app(FederationFetcher::class)->fetchPeer($peer);
        app(FederationFetcher::class)->fetchPeer($peer);

        $this->assertSame(1, FederatedContentItem::count());
    }

    public function test_an_unreachable_peer_is_skipped_not_fatal(): void
    {
        Http::fake([
            'unreachable.example/*' => Http::response(null, 500),
        ]);

        $peer = FederationPeer::create([
            'base_url' => 'https://unreachable.example',
            'label' => 'Down',
            'subscribed_content_types' => ['website'],
        ]);

        $count = app(FederationFetcher::class)->fetchPeer($peer);

        $this->assertSame(0, $count);
        $this->assertSame(0, FederatedContentItem::count());
    }

    public function test_the_owner_can_manage_peers_through_the_api(): void
    {
        $response = $this->postJson('/api/federation/peers', [
            'base_url' => 'https://peer.example',
            'label' => 'Peer Instance',
            'subscribed_content_types' => ['website', 'public-information'],
        ])->assertCreated();

        $peerId = $response->json('id');

        $this->getJson('/api/federation/peers')->assertOk()->assertJsonFragment(['label' => 'Peer Instance']);

        $this->deleteJson("/api/federation/peers/{$peerId}")->assertNoContent();
        $this->assertDatabaseMissing('federation_peers', ['id' => $peerId]);
    }

    public function test_a_user_without_instance_manage_cannot_manage_peers(): void
    {
        $this->actingAs(User::factory()->create());

        $this->getJson('/api/federation/peers')->assertForbidden();
        $this->postJson('/api/federation/peers', [
            'base_url' => 'https://peer.example', 'label' => 'x', 'subscribed_content_types' => ['website'],
        ])->assertForbidden();
    }
}
