<?php

namespace App\Http\Controllers;

use App\Kernel\FederationFetcher;
use App\Models\FederationPeer;
use Illuminate\Http\Request;

/**
 * instance.manage gated, same as Team and Marketplace install/uninstall
 * — adding a peer is the same class of instance-wide configuration
 * action. The content types below are the only ones FederationFeed
 * currently produces (Fed-a); extending to Events/Directories later
 * means extending this list, not redesigning it.
 */
class FederationPeerController extends Controller
{
    private const CONTENT_TYPES = ['website', 'knowledge-base', 'public-information'];

    public function index()
    {
        return FederationPeer::withCount('content')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'base_url' => ['required', 'url', 'unique:federation_peers,base_url'],
            'label' => ['required', 'string', 'max:255'],
            'subscribed_content_types' => ['required', 'array', 'min:1'],
            'subscribed_content_types.*' => ['string', 'in:'.implode(',', self::CONTENT_TYPES)],
        ]);

        return response()->json(FederationPeer::create($validated), 201);
    }

    public function destroy(FederationPeer $peer)
    {
        $peer->delete();

        return response()->noContent();
    }

    public function fetchNow(FederationPeer $peer, FederationFetcher $fetcher)
    {
        $count = $fetcher->fetchPeer($peer->fresh());

        return ['fetched' => $count, 'peer' => $peer->fresh()];
    }
}
