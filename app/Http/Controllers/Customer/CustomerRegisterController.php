<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;

use App\CustomerRegister;
use App\Model\CustomerDetail as customer_details;
use Illuminate\Http\Request;
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

        // input validation
        $validation = Validator::make($request->all(), [
            'username' => ['required', 'unique:dbmarketsellers.seller_logins,seller_username', 'unique:dbmarketcustomers.customer_logins,customer_username'],
            'password' => ['required', 'min:8', 'max:12', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*[\d]).{8,12}$/'],
            'email' => ['required', 'email', 'unique:dbmarketsellers.seller_details,seller_email', 'unique:dbmarketcustomers.customer_details,customer_email'],
            'customer_dob' => ['date'],
            'customer_sex' => ['boolean'], // 0 Female, 1 Male
            'customer_phone' => ['numeric'],
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
        $request->request->add(['customer_id' => $customerID]);

        // send to CustomerLoginController to create login account
        $customerLogin = new CustomerLoginController;
        $status = $customerLogin->create($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $response = [
            'status' => 200,
            'customer_username' => $request->username,
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
    public function show(...$customerID)
    {
        $customerID = collect($customerID);
        $customerID = $customerID->flatten();
        $customerDetails = customer_details::whereIn('id', $customerID)->get();
        $customerIdCheck = $customerDetails->map(function ($item) {
            return $item->id;
        });
        $status = $customerID->diff($customerIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 404,
                'message' => 'Data not found',
                'data' => $status->all()
            ];
            return response()->json($response, 404);
        }
        $customerDetails = $customerDetails->map(function ($item) {
            $item = [
                'id' => $item->id,
                'username' => $item->customer_username,
                'fullname' => $item->customer_fullname,
                'dob' => $item->customer_dob,
                'address' => $item->customer_address,
                'sex' => $item->customer_sex == 0 ? 'female' : 'male',
                'email' => $item->customer_email,
                'phone' => $item->customer_phone,
                'join_date' => date_format($item->created_at, 'Y-m-d H:i:s')
            ];
            return $item;
        });
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
