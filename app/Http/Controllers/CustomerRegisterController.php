<?php

namespace App\Http\Controllers;

use App\CustomerRegister;
use App\Model\CustomerDetail as customer_details;
use App\Model\CustomerLogin as customer_logins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CustomerRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customerData = [];
        $customerDetails = customer_details::all();
        foreach ($customerDetails as $customer => $detail) {
            $customerData[] = [
                'id' => $detail->id,
                'username' => $detail->customer_username,
                'fullname' => $detail->customer_fullname,
                'dob' => $detail->customer_dob,
                'address' => $detail->customer_address,
                'sex' => $detail->customer_sex == 0 ? 'female' : 'male',
                'email' => $detail->customer_email,
                'phone' => $detail->customer_phone,
                'join_date' => date_format($detail->created_at, 'Y-m-d H:i:s')
            ];
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
        // create coin account
        $request->request->add(['account_type' => 1]);
        $coinDetails = new CoinRegisterController;
        $status = $coinDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // input validation
        $validation = Validator::make($request->all(), [
            'username' => ['required', 'unique:dbmarketsellers.seller_logins,seller_username', 'unique:dbmarketcustomers.customer_logins,customer_username'],
            'password' => ['required', 'min:8', 'max:12', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*[\d]).{8,12}$/'],
            'email' => ['required', 'email', 'unique:dbmarketsellers.seller_details,seller_email', 'unique:dbmarketcustomers.customer_details,customer_email']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 401,
                'message' => $validation->errors()
            ];
            return response()->json($response, 401);
        }

        $customerDetail = [
            'customer_fullname' => $request->input('fullname') ?: $request->input('username'),
            'customer_dob' => $request->input('dob'),
            'customer_address' => $request->input('address'),
            'customer_sex' => $request->input('sex'),
            'customer_email' => $request->input('email'),
            'customer_phone' => $request->input('phone'),
            'customer_username' => $request->input('username'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $customerID = customer_details::insertGetId($customerDetail);
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
            'customer_username' => $customerLogin['customer_username'],
            'token' => customer_details::find($customerID)->createToken('register_token', ['customer'])->accessToken
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerRegister  $customerRegister
     * @return \Illuminate\Http\Response
     */
    public function show($customerID)
    {
        $customerDetails = customer_details::where('id', $customerID);
        if (!$customerDetails->exists()) {
            $response = [
                'status' => 404,
                'message' => 'Data not found'
            ];
            return response()->json($response, 404);
        }
        $customerDetails = $customerDetails->first();
        $customerDetails = [
            'id' => $customerDetails->id,
            'username' => $customerDetails->customer_username,
            'fullname' => $customerDetails->customer_fullname,
            'dob' => $customerDetails->customer_dob,
            'address' => $customerDetails->customer_address,
            'sex' => $customerDetails->customer_sex == 0 ? 'female' : 'male',
            'email' => $customerDetails->customer_email,
            'phone' => $customerDetails->customer_phone,
            'join_date' => date_format($customerDetails->created_at, 'Y-m-d H:i:s')
        ];
        $response = [
            'status' => 200,
            'data' => $customerDetails
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerRegister  $customerRegister
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerRegister $customerRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerRegister  $customerRegister
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerRegister $customerRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerRegister  $customerRegister
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerRegister $customerRegister)
    {
        //
    }
}
