<?php

namespace App\Http\Middleware;

use App\Model\CustomerLogin as customer_login;
use App\Model\SellerLogin as seller_login;
use Closure;

class AuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('Authorization');
        // search in customer
        $tokenExsist = customer_login::where('customerToken', $token)->exists();
        if ($tokenExsist) {
            $next($request);
        } else {
            // search in sellers
            $tokenExsist = seller_login::where('sellerToken', $token)->exists();
            if ($tokenExsist) {
                $next($request);
            } else {
                $response = [
                    'status' => 401,
                    'Message' => "You're Not Authorized"
                ];
                return response()->json($response, 401);
            }
        }
    }
}
