<?php

namespace Tests\Feature\Kernel;

use App\Kernel\ModuleInstaller;
use Illuminate\Support\Facades\Auth;
use Moduloom\KnowledgeBase\Models\Article;
use Moduloom\PublicInformation\Models\Notice;
use Moduloom\Website\Models\WebsitePage;
use Tests\TestCase;

/**
 * Fed-a: the outbound side of federation. These feeds mirror content
 * that's already public and unauthenticated on /site, /kb, and
 * /notices — so, same as those pages, no login should ever be required
 * to read one.
 */
class FederationFeedTest extends TestCase
{
    public function test_the_website_feed_lists_pages_with_no_login_required(): void
    {
        app(ModuleInstaller::class)->install('website', 'manual', 'owner');
        WebsitePage::create(['title' => 'About', 'slug' => 'about', 'content' => 'We make things.']);

        Auth::logout();

        $response = $this->getJson('/federation/website.json')->assertOk();
        $response->assertJsonPath('content_type', 'website');
        $response->assertJsonPath('instance.name', config('app.name'));
        $response->assertJsonFragment([
            'title' => 'About',
            'slug' => 'about',
            'content' => 'We make things.',
            'canonical_url' => url('/site/about'),
        ]);
    }

    public function test_the_knowledge_base_feed_lists_articles(): void
    {
        app(ModuleInstaller::class)->install('knowledge-base', 'manual', 'owner');
        Article::create(['title' => 'Getting started', 'slug' => 'getting-started', 'category' => 'Basics', 'content' => 'Read this first.']);

        Auth::logout();

        $this->getJson('/federation/knowledge-base.json')
            ->assertOk()
            ->assertJsonPath('content_type', 'knowledge-base')
            ->assertJsonFragment(['title' => 'Getting started', 'canonical_url' => url('/kb/getting-started')]);
    }

    public function test_the_public_information_feed_lists_notices_with_a_fragment_canonical_url(): void
    {
        app(ModuleInstaller::class)->install('public-information', 'manual', 'owner');
        $notice = Notice::create(['title' => 'Office closed Monday', 'body' => 'For the holiday.', 'published_at' => now()]);

        Auth::logout();

        $this->getJson('/federation/public-information.json')
            ->assertOk()
            ->assertJsonPath('content_type', 'public-information')
            ->assertJsonFragment([
                'title' => 'Office closed Monday',
                'slug' => null,
                'canonical_url' => url('/notices')."#notice-{$notice->id}",
            ]);
    }

    public function test_an_orphaned_modules_feed_is_unreachable_same_as_its_public_page(): void
    {
        app(ModuleInstaller::class)->install('website', 'manual', 'owner');
        app(ModuleInstaller::class)->uninstall('website', 'manual', 'owner');

        Auth::logout();

        $this->getJson('/federation/website.json')->assertNotFound();
    }
}
