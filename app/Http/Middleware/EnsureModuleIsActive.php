<?php

namespace App\Http\Middleware;

use App\Kernel\Reconciler;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates a module's own routes behind the kernel's own notion of
 * "installed." An orphaned module's routes 404 — uninstalling through
 * the kernel has to actually mean something from the outside, not just
 * change a row nothing else looks at.
 */
class EnsureModuleIsActive
{
    public function __construct(private readonly Reconciler $reconciler) {}

    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        if (! $this->reconciler->isInstalled($moduleSlug)) {
            abort(404);
        }

        return $next($request);
    }
}
