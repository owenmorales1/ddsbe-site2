<?php

namespace App\Http\Middleware;

use Closure;

class AuthenticateAccess
{
    public function handle($request, Closure $next)
    {
        $acceptedSecrets = explode(',', env('ACCEPTED_SECRETS'));

        $header = $request->header('Authorization');

        // Check if header exists
        if (!$header) {
            return response()->json([
                'error' => 'Unauthorized. Missing token.'
            ], 401);
        }

        // Extract Bearer token
        if (strpos($header, 'Bearer ') === 0) {
            $token = substr($header, 7);
        } else {
            $token = $header; // fallback (if not using Bearer)
        }

        // Trim spaces (IMPORTANT)
        $token = trim($token);

        if (!in_array($token, $acceptedSecrets)) {
            return response()->json([
                'error' => 'Unauthorized. Invalid secret key.'
            ], 401);
        }

        return $next($request);
    }
}