<?php

namespace App\Http\Middleware;

use App\Services\AdminAuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditAdminAction
{
    public function __construct(private readonly AdminAuditService $audit) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        if ($user?->hasAnyRole(['admin', 'Marketing'])
            && ! in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)
            && $response->getStatusCode() < 400) {
            $routeName = $request->route()?->getName() ?: $request->path();
            $this->audit->record(
                'admin.request.'.strtolower($request->method()),
                null,
                "Action administrative exécutée : {$routeName}.",
                ['route' => $routeName, 'method' => $request->method()]
            );
        }

        return $response;
    }
}
