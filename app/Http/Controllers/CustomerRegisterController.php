<?php

namespace App\Http\Controllers;

use App\CustomerRegister;
use App\Model\CustomerDetail as customer_details;
use App\Model\CustomerLogin as customer_logins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
                'username' => $detail->customerUsername,
                'fullname' => $detail->customerFullname,
                'DOB' => $detail->customerDOB,
                'address' => $detail->customerAddress,
                'sex' => $detail->customerSex == 0 ? 'Male' : 'Female',
                'email' => $detail->customerEmail,
                'phone' => $detail->customerPhone,
                'joinDate' => date_format($detail->created_at, 'Y-m-d H:i:s')
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
        $accountExist = customer_details::where('customerUsername', $request->input('username'))->exists();
        if ($accountExist) {
            $response = [
                'status' => 401,
                'message' => 'Account was exists'
            ];
            return response()->json($response, 401);
        }
        $customerDetail = [
            'customerFullname' => $request->input('fullname') ?: $request->input('username'),
            'customerDOB' => $request->input('DOB'),
            'customerAddress' => $request->input('address'),
            'customerSex' => $request->input('sex'),
            'customerEmail' => $request->input('email'),
            'customerPhone' => $request->input('phone'),
            'customerUsername' => $request->input('username'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $customerID = customer_details::insertGetId($customerDetail);
        $customerLogin = [
            'customerUsername' => $request->input('username'),
            'customerPassword' => Hash::make($request->input('password')),
            'customerToken' => Str::random(60),
            'customerStatus' => 1,
            'customerId' => $customerID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = customer_logins::insert($customerLogin);
        if ($status) {
            $response = [
                'status' => 200,
                'customerUsername' => $customerLogin['customerUsername'],
                'token' => $customerLogin['customerToken']
            ];
            return response()->json($response, 200);
        }
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
            'username' => $customerDetails->customerUsername,
            'fullname' => $customerDetails->customerFullname,
            'DOB' => $customerDetails->customerDOB,
            'address' => $customerDetails->customerAddress,
            'sex' => $customerDetails->customerSex == 0 ? 'Male' : 'Female',
            'email' => $customerDetails->customerEmail,
            'phone' => $customerDetails->customerPhone,
            'joinDate' => date_format($customerDetails->created_at, 'Y-m-d H:i:s')
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
