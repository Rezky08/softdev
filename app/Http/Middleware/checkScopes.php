<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;

class checkScopes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        $guard = ['customer', 'seller'];
        foreach ($guard as $guardname) {
            $guardname = Auth::guard($guardname)->user();
            if ($guardname) {
                if (is_array($scopes)) {
                    foreach ($scopes as $scopesTemplate) {
                        if ($guardname->tokenCan($scopesTemplate)) {
                            $request->request->add([$scopesTemplate . 'Data' => $guardname]);
                            return $next($request);
                        }
                    }
                }
                break;
            }
        }

        $response = [
            'status' => 401,
            'Message' => "You're Not Authorized"
        ];
        return response()->json($response, 401);
    }
}
