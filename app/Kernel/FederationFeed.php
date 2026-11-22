<?php

namespace App\Kernel;

use Illuminate\Http\JsonResponse;

/**
 * Fed-a: the one envelope every federatable module's feed returns, so
 * a peer instance consuming any of them (Fed-b) needs exactly one
 * parser, not one per content type. Each module maps its own model
 * into the common item shape (title, slug, content, published_at,
 * canonical_url) before handing it here — this class only ever wraps,
 * never reaches into a module's own schema.
 */
class FederationFeed
{
    public static function respond(string $contentType, iterable $items): JsonResponse
    {
        return response()->json([
            'instance' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'content_type' => $contentType,
            'items' => collect($items)->values(),
        ]);
    }
}
