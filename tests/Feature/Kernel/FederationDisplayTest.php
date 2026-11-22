<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ModuleInstaller;
use App\Models\FederatedContentItem;
use App\Models\FederationPeer;
use Illuminate\Support\Facades\Auth;
use Moduloom\PublicInformation\Models\Notice;
use Tests\TestCase;

/**
 * Fed-c: federated content has to actually be visible somewhere, and
 * always visibly attributed — never indistinguishable from this
 * instance's own content. /notices already had a listing to extend;
 * Website and Knowledge base have no listing page at all (only direct
 * slug lookups), so /federation/browse is their display surface.
 */
class FederationDisplayTest extends TestCase
{
    private function cachePeerItem(string $contentType, string $title): FederatedContentItem
    {
        $peer = FederationPeer::create([
            'base_url' => 'https://peer.example',
            'label' => 'Peer Instance',
            'subscribed_content_types' => [$contentType],
        ]);

        return FederatedContentItem::create([
            'federation_peer_id' => $peer->id,
            'content_type' => $contentType,
            'title' => $title,
            'content' => 'Content from the peer.',
            'canonical_url' => 'https://peer.example/site/example',
            'published_at' => now(),
            'fetched_at' => now(),
        ]);
    }

    public function test_notices_page_shows_federated_notices_attributed_alongside_local_ones(): void
    {
        app(ModuleInstaller::class)->install('public-information', 'manual', 'owner');
        Notice::create(['title' => 'Local Notice', 'body' => 'Ours.', 'published_at' => now()]);
        $this->cachePeerItem('public-information', 'Peer Notice');

        Auth::logout();

        $this->get('/notices')
            ->assertOk()
            ->assertSee('Local Notice')
            ->assertSee('Peer Notice')
            ->assertSee('Peer Instance')
            ->assertSee('From other instances');
    }

    public function test_federation_browse_page_lists_federated_content_grouped_by_type_with_no_login(): void
    {
        $this->cachePeerItem('website', 'Peer Website Page');

        Auth::logout();

        $this->get('/federation/browse')
            ->assertOk()
            ->assertSee('Peer Website Page')
            ->assertSee('Peer Instance')
            ->assertSee('Website');
    }

    public function test_federation_browse_page_shows_an_empty_state_with_nothing_cached(): void
    {
        Auth::logout();

        $this->get('/federation/browse')
            ->assertOk()
            ->assertSee('No federated content yet');
    }

    public function test_the_peer_list_api_reports_a_real_content_count(): void
    {
        $this->cachePeerItem('website', 'Peer Website Page');

        $response = $this->getJson('/api/federation/peers')->assertOk();
        $response->assertJsonFragment(['content_count' => 1]);
    }
}
