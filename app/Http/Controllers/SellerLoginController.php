<?php

namespace App\Http\Controllers;

// use App\SellerLogin;

use App\Model\SellerDetail as seller_details;
use App\Model\SellerLogin as seller_logins;
use App\Model\SellerLoginLog as seller_login_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class SellerLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerData = Auth::guard('seller')->user();
        if (!$customerData) {
            $response = [
                'status' => 401,
                'Message' => "You're Not Authorized"
            ];
            return response()->json($response, 401);
        }
        $response = [
            'status' => 200,
            'data' => $customerData
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // input Validation
        $validation = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required', 'min:8', 'max:12']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 400,
                'message' => $validation->errors()
            ];
            return response()->json($response, 403);
        }

        $username_input = $request->username;
        $password_input = $request->password;
        $sellerLogin  = seller_details::where('sellerUsername', $username_input);
        if (!$sellerLogin->exists()) {
            // Redirect to login page again
            $response = [
                'status' => 403,
                'message' => 'Login Failed'
            ];
            return response()->json($response, 403);
        }
        $sellerLogin = seller_logins::where('sellerUsername', $username_input)->first();
        $loginInfo = [
            'sellerId' => $sellerLogin->sellerId,
            'sellerUsername' => $sellerLogin->sellerUsername,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        if (Hash::check($password_input, $sellerLogin->sellerPassword)) {
            $loginInfo['loginSuccess'] = 1;
            seller_login_logs::insert($loginInfo);
            $sellerData = seller_details::where('id', $loginInfo['sellerId'])->first();
            // unset($loginInfo['created_at'], $loginInfo['updated_at']);
            // $loginData = [
            //     'id' => $sellerData->id,
            //     'username' => $sellerData->sellerUsername,
            //     'fullname' => $sellerData->sellerFullname,
            //     'DOB' => $sellerData->sellerDOB,
            //     'address' => $sellerData->sellerAddress,
            //     'sex' => $sellerData->sellerSex == 0 ? 'Male' : 'Female',
            //     'email' => $sellerData->sellerEmail,
            //     'phone' => $sellerData->sellerPhone,
            //     'loginTime' => date_format(now(), 'Y-m-d H:i:s')
            // ];
            $response = [
                'status' => 200,
                // 'data' => $loginData,
                'token' => $sellerData->createToken('loginToken', ['seller'])->accessToken,
            ];
            return response()->json($response, 200);
        }
        // Redirect to login page again
        $response = [
            'status' => 403,
            'message' => 'Login Failed'
        ];
        $loginInfo['loginSuccess'] = 0;
        seller_login_logs::insert($loginInfo);
        return response()->json($response, 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerLogin  $sellerLogin
     * @return \Illuminate\Http\Response
     */
    public function show(SellerLogin $sellerLogin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellerLogin  $sellerLogin
     * @return \Illuminate\Http\Response
     */
    public function edit(SellerLogin $sellerLogin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerLogin  $sellerLogin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellerLogin $sellerLogin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerLogin  $sellerLogin
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerLogin $sellerLogin)
    {
        //
    }
}
