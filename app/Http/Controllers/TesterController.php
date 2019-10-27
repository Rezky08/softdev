<?php

namespace App\Http\Controllers;

use App\Model\CustomerDetail as customer_details;
use App\Model\CustomerLogin as customer_logins;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SebastianBergmann\CodeCoverage\Report\Html\File;

class TesterController extends Controller
{

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $response = [
            'message' => Str::random(60),
            'data' => $customer

        ];
        return response()->json($response, 200);
    }
    public function login(Request $request)
    {
        $customer = customer_logins::where('customerUsername', $request->input('username'))->first();
        if ($customer) {
            if (Hash::check($request->input('password'), $customer->customerPassword)) {
                $customer = customer_details::where('id', $customer->customerId)->first();
                $customer = $customer->createToken('Login', ['customer'])->accessToken;
                return response()->json($customer);
            }
        }
        return response()->json('NotAuthorized', 200);
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
        $credentials = [
            'customerUsername' => 'rezky221196'
        ];
        $user = CustomerLoginJWT::where($credentials);
        $user = $user->first();
        $JWT = JWTAuth::fromUser($user);
        return var_dump($JWT);
    }
}
