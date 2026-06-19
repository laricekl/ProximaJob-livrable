<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException as BaseAuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationException extends BaseAuthorizationException
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Accès interdit.'
            ], 403);
        }

        return redirect()->back()->with('forbidden', 'Vous n\'avez pas le droit d’accéder à cette page.');
    }
}
