<?php

namespace App\Http\Controllers;

use App\Kernel\ThemeManager;
use App\Models\FederatedContentItem;

/**
 * Fed-c's display surface for Website and Knowledge base federated
 * content — neither module has a public listing page of its own
 * (/site/{slug} and /kb/{slug} are direct lookups only), so there's no
 * existing page to blend federated items into the way /notices already
 * could. Public information's own /notices got that treatment directly
 * (PublicNoticesController); this is the catch-all for the other two,
 * and shows every content type for consistency rather than splitting
 * attention across several near-empty single-type pages.
 */
class FederationBrowseController extends Controller
{
    public function index(ThemeManager $themes)
    {
        $items = FederatedContentItem::with('peer')->latest('published_at')->get()->groupBy('content_type');

        return $themes->wrap(view('federation-browse', ['itemsByType' => $items]));
    }
}
