<?php

namespace App\Http\Controllers;

// use App\SupplierLogin;

use App\Model\SupplierDetail as supplier_details;
use App\Model\SupplierLogin as supplier_logins;
use App\Model\SupplierLoginLog as supplier_login_logs;
use App\Model\SupplierShop as supplier_shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class SupplierLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierData = Auth::guard('supplier')->user();
        if (!$supplierData) {
            $response = [
                'status' => 401,
                'message' => "You're Not Authorized"
            ];
            return response()->json($response, 401);
        }
        $response = [
            'status' => 200,
            'data' => $supplierData
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
        $supplierID = $request->supplier_id;
        $supplierLogin = [
            'supplier_username' => $request->input('username'),
            'supplier_password' => Hash::make($request->input('password')),
            'supplier_status' => 1,
            'supplier_id' => $supplierID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = supplier_logins::insert($supplierLogin);
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
        $supplierLogin  = supplier_details::where('supplier_details.supplier_username', $username_input)->leftJoin('supplier_logins', 'supplier_details.supplier_username', '=', 'supplier_logins.supplier_username');
        if (!$supplierLogin->exists()) {
            $response = [
                'status' => 403,
                'message' => 'You have entered an invalid username or password'
            ];
            return response()->json($response, 403);
        }
        $supplierLogin = $supplierLogin->first();
        $loginInfo = [
            'supplier_id' => $supplierLogin->supplier_id,
            'supplier_username' => $supplierLogin->supplier_username,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        if (Hash::check($password_input, $supplierLogin->supplier_password)) {
            $loginInfo['login_success'] = 1;
            $request->request->add(['login_info' => $loginInfo]);

            // create login log
            $supplierLoginLog = new SupplierLoginLogController;
            $status = $supplierLoginLog->store($request);
            if ($status->getStatusCode() != 200) {
                return $status;
            }
            //

            $supplierData = $supplierLogin;
            $response = [
                'status' => 200,
                // 'data' => $loginData,
                'token' => $supplierData->createToken('login_token', ['supplier'])->accessToken,
            ];
            return response()->json($response, 200);
        }

        $loginInfo['login_success'] = 0;
        $request->request->add(['login_info' => $loginInfo]);

        // create login log
        $supplierLoginLog = new SupplierLoginLogController;
        $status = $supplierLoginLog->store($request);
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
     * @param  \App\SupplierLogin  $supplierLogin
     * @return \Illuminate\Http\Response
     */
    public function show(SupplierLogin $supplierLogin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SupplierLogin  $supplierLogin
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierLogin $supplierLogin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SupplierLogin  $supplierLogin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierLogin $supplierLogin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SupplierLogin  $supplierLogin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $supplierData = $request->supplierData;
        if ($supplierData->token()->revoke()) {
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
