<?php

namespace Moduloom\PublicInformation\Http\Controllers;

use App\Kernel\FederationFeed;
use App\Kernel\ThemeManager;
use App\Models\FederatedContentItem;
use Illuminate\Routing\Controller;
use Moduloom\PublicInformation\Models\Notice;

class PublicNoticesController extends Controller
{
    public function index(ThemeManager $themes)
    {
        $notices = Notice::query()->latest('published_at')->get();

        // Fed-c: federated notices show on the same page local ones do
        // — but always as a visibly separate, attributed group, never
        // blended in as if they were this instance's own.
        $federated = FederatedContentItem::with('peer')
            ->where('content_type', 'public-information')
            ->latest('published_at')
            ->get();

        return $themes->wrap(view('public-information::notices', [
            'notices' => $notices,
            'federated' => $federated,
        ]));
    }

    public function feed()
    {
        // Notices have no individual detail page — /notices lists all
        // of them on one page — so the closest honest canonical_url is
        // that listing page with a fragment, not a URL that doesn't
        // otherwise exist.
        $items = Notice::query()->latest('published_at')->get()->map(fn (Notice $notice) => [
            'title' => $notice->title,
            'slug' => null,
            'content' => $notice->body,
            'published_at' => $notice->published_at?->toIso8601String(),
            'canonical_url' => url('/notices')."#notice-{$notice->id}",
        ]);

        return FederationFeed::respond('public-information', $items);
    }
}
