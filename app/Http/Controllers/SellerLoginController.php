<?php

namespace App\Http\Controllers;

// use App\SellerLogin;

use App\Model\SellerDetail as seller_details;
use App\Model\SellerLogin as seller_logins;
use App\Model\SellerLoginLog as seller_login_logs;
use App\Model\SellerShop as seller_shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $sellerData = Auth::guard('seller')->user();
        $sellerData->shop;
        if (!$sellerData) {
            $response = [
                'status' => 401,
                'message' => "You're Not Authorized"
            ];
            return response()->json($response, 401);
        }
        $response = [
            'status' => 200,
            'data' => $sellerData
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $sellerID = $request->seller_id;
        $sellerLogin = [
            'seller_username' => $request->input('username'),
            'seller_password' => Hash::make($request->input('password')),
            'seller_status' => 1,
            'seller_id' => $sellerID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = seller_logins::insert($sellerLogin);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Login account has been created',
        ];
        return response()->json($response, 200);
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
        $sellerLogin  = seller_details::where('seller_details.seller_username', $username_input)->leftJoin('seller_logins', 'seller_details.seller_username', '=', 'seller_logins.seller_username');
        if (!$sellerLogin->exists()) {
            $response = [
                'status' => 403,
                'message' => 'You have entered an invalid username or password'
            ];
            return response()->json($response, 403);
        }
        $sellerLogin = $sellerLogin->first();
        $loginInfo = [
            'seller_id' => $sellerLogin->seller_id,
            'seller_username' => $sellerLogin->seller_username,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        if (Hash::check($password_input, $sellerLogin->seller_password)) {
            $loginInfo['login_success'] = 1;
            $request->request->add(['login_info' => $loginInfo]);

            // create login log
            $sellerLoginLog = new SellerLoginLogController;
            $status = $sellerLoginLog->store($request);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            //

            $sellerData = $sellerLogin;
            $response = [
                'status' => 200,
                // 'data' => $loginData,
                'token' => $sellerData->createToken('login_token', ['seller'])->accessToken,
            ];
            return response()->json($response, 200);
        }

        $loginInfo['login_success'] = 0;
        $request->request->add(['login_info' => $loginInfo]);

        // create login log
        $sellerLoginLog = new SellerLoginLogController;
        $status = $sellerLoginLog->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }
        //

        $response = [
            'status' => 403,
            'message' => 'You have entered an invalid username or password'
        ];
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
    public function destroy(Request $request)
    {
        $sellerData = $request->sellerData;
        if ($sellerData->token()->revoke()) {
            $response = [
                'status' => 200,
                'message' => 'You Have Logged Out'
            ];
            return response()->json($response);
        }
        $response = [
            'status' => 401,
            'message' => "You're Not Authorized"
        ];
        return response()->json($response, 401);
    }
}
