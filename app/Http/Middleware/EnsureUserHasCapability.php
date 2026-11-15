<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 6b: the check EnsureModuleIsActive never did — being logged in
 * (Phase 6a) only proves an identity, not that it holds the role a
 * given route requires. Sits alongside module.active on each module's
 * own routes rather than a central kernel-wide map, same reasoning:
 * capability requirements stay co-located with the routes they guard.
 */
class EnsureUserHasCapability
{
    public function handle(Request $request, Closure $next, string $capabilitySlug): Response
    {
        if (! $request->user()?->hasCapability($capabilitySlug)) {
            abort(403);
        }

        return $next($request);
    }
}
