<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->verified_at === null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Your phone number is not verified.'
            ], 409);
        }

        return $next($request);
    }
}
