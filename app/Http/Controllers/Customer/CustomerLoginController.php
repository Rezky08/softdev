<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

// use App\CustomerLogin;

use App\Model\CustomerDetail as customer_details;
use App\Model\CustomerLogin as customer_logins;
use App\Model\CustomerLoginLog as customer_login_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class CustomerLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerData = Auth::guard('customer')->user();
        if (!$customerData) {
            $response = [
                'status' => 401,
                'message' => "You're Not Authorized"
            ];
            return response()->json($response, 401);
        }
        $customerData = [
            'id' => $customerData->id,
            'username' => $customerData->customer_username,
            'fullname' => $customerData->customer_fullname,
            'dob' => $customerData->customer_dob,
            'address' => $customerData->customer_address,
            'sex' => $customerData->customer_sex == 0 ? 'female' : 'male',
            'email' => $customerData->customer_email,
            'phone' => $customerData->customer_phone,
            'join_date' => date_format($customerData->created_at, 'Y-m-d H:i:s')
        ];
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
    public function create(Request $request)
    {
        $customerID = $request->customer_id;
        $customerLogin = [
            'customer_username' => $request->input('username'),
            'customer_password' => Hash::make($request->input('password')),
            'customer_status' => 1,
            'customer_id' => $customerID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = customer_logins::insert($customerLogin);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status' => 200,
            'message' => 'Login account has been created'
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
        $customerLogin  = customer_details::where('customer_details.customer_username', $username_input)->leftJoin('customer_logins', 'customer_details.customer_username', '=', 'customer_logins.customer_username');
        if (!$customerLogin->exists()) {
            $response = [
                'status' => 403,
                'message' => 'You have entered an invalid username or password'
            ];
            return response()->json($response, 403);
        }
        $customerLogin = $customerLogin->first();
        $loginInfo = [
            'customer_id' => $customerLogin->customer_id,
            'customer_username' => $customerLogin->customer_username,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        if (Hash::check($password_input, $customerLogin->customer_password)) {
            $loginInfo['login_success'] = 1;
            $request->request->add(['login_info' => $loginInfo]);

            // create login log
            $customerLoginLog = new CustomerLoginLogController;
            $status = $customerLoginLog->store($request);
            //
            if ($status->getStatusCode() != 200) {
                return $status;
            }

            $customerData = $customerLogin;
            $response = [
                'status' => 200,
                // 'data' => $loginData,
                'token' => $customerData->createToken('login_token', ['customer'])->accessToken,
            ];
            return response()->json($response, 200);
        }
        // Redirect to login page again

        $loginInfo['login_success'] = 0;
        $request->request->add(['login_info' => $loginInfo]);

        // create login log
        $customerLoginLog = new CustomerLoginLogController;
        $status = $customerLoginLog->store($request);
        //

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $response = [
            'status' => 403,
            'message' => 'You have entered an invalid username or password'
        ];
        return response()->json($response, 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $customerData = $request->customerData;
        if ($customerData->token()->revoke()) {
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
