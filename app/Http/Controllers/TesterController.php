<?php

namespace App\Http\Controllers;

use App\Model\CustomerLogin as customer_logins;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\File;
use JWTAuth;
use JWTFactory;

class TesterController extends Controller
{
    public function index(Request $request)
    {
        $customerGuard = Auth::guard('customer');
        $credentials = [
            'customerUsername' => 'rezky221197',
        ];

        $user = customer_logins::where($credentials)->first();
        $response = [
            'message' => Str::random(60),
            'JWT' => $customerGuard->fromUser($user),
            'parseToken' => $customerGuard->parseToken()->getPayload()

        ];
        return response()->json($response, 200);
    }
    public function readFile(Request $request)
    {
        $productImage = $request->file('productImage');
        $productImage->store('public/Sellers/Product/');
        if ($productImage->getClientSize() <= 100) {
            return response()->json($productImage->getClientSize());
        } else {
            return response()->json('Melebihi batas');
        }
    }
    public function paramTest($param, $param2)
    {
        var_dump($param, $param2);
    }
    public function JWTTest()
    {
        $customClaims = ['foo' => 'bar', 'baz' => 'bob'];
        // $payload = JWTFactory::make($customClaims);
        $token = Auth::guard('customer')->fromUser($customClaims);
        return response()->json($token, 200);
    }
}
