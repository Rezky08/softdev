<?php

namespace App\Http\Controllers;

// use App\CustomerLogin;

use App\Model\CustomerDetail as customer_details;
use App\Model\CustomerLogin as customer_logins;
use App\Model\CustomerLoginLog as customer_login_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('login');
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

        $username_input = $request->username;
        $password_input = $request->password;
        $customerLogin  = customer_details::where('customerUsername', $username_input);
        if (!$customerLogin->exists()) {
            // Redirect to login page again
            $response = [
                'status' => 403,
                'message' => 'Login Failed'
            ];
            return response()->json($response, 403);
        }
        $customerLogin = customer_logins::where('customerUsername', $username_input)->first();
        $loginInfo = [
            'customerId' => $customerLogin->customerId,
            'customerUsername' => $customerLogin->customerUsername,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        if (Hash::check($password_input, $customerLogin->customerPassword)) {
            $loginInfo['loginSuccess'] = 1;
            customer_login_logs::insert($loginInfo);
            // Redirect to home or last pagez
            $response = [
                'status' => 200,
                'token' => $customerLogin->customerToken
            ];
            return response()->json($response, 200);
        }
        // Redirect to login page again
        $response = [
            'status' => 403,
            'message' => 'Login Failed'
        ];
        $loginInfo['loginSuccess'] = 0;
        customer_login_logs::insert($loginInfo);
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
    public function destroy(CustomerLogin $customerLogin)
    {
        //
    }
}
