<?php

namespace App\Http\Controllers\Supplier;

use App\Http\Controllers\Controller;

use App\SupplierRegister;
use App\Model\SupplierDetail as supplier_details;
use App\Model\SupplierLogin as supplier_logins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class SupplierRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplierDetails = supplier_details::all();
        $supplierDetails = $supplierDetails->map(function ($detail) {
            $supplierData = [
                'id' => $detail->id,
                'username' => $detail->supplier_username,
                'fullname' => $detail->supplier_fullname,
                'dob' => $detail->supplier_dob,
                'address' => $detail->supplier_address,
                'sex' => $detail->supplier_sex == 0 ? 'female' : 'male',
                'email' => $detail->supplier_email,
                'phone' => $detail->supplier_phone,
                'join_date' => date_format($detail->created_at, 'Y-m-d H:i:s')
            ];
            return $supplierData;
        });
        $response = [
            'status' => 200,
            'data' => $supplierDetails
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
            'username' => ['required', 'unique:dbmarketsuppliers.supplier_logins,supplier_username', 'unique:dbmarketsellers.seller_logins,seller_username', 'unique:dbmarketcustomers.customer_logins,customer_username'],
            'password' => ['required', 'min:8', 'max:12', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*[\d]).{8,12}$/'],
            'email' => ['required', 'email', 'unique:dbmarketsuppliers.supplier_details,supplier_email', 'unique:dbmarketsellers.seller_details,seller_email', 'unique:dbmarketcustomers.customer_details,customer_email'],
            'customer_dob' => ['date'],
            'customer_sex' => ['boolean'],
            'customer_phone' => ['numeric'],
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 401,
                'message' => $validation->errors()
            ];
            return response()->json($response, 401);
        }

        $supplierDetail = [
            'supplier_fullname' => $request->input('fullname') ?: $request->input('username'),
            'supplier_dob' => $request->input('dob'),
            'supplier_address' => $request->input('address'),
            'supplier_sex' => $request->input('sex'),
            'supplier_email' => $request->input('email'),
            'supplier_phone' => $request->input('phone'),
            'supplier_username' => $request->input('username'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $supplierID = supplier_details::insertGetId($supplierDetail);
        if (!$supplierID) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            return response()->json($response, 500);
        }
        $request->request->add(['supplier_id' => $supplierID]);

        // send to SupplierLoginController to create l account
        $supplierLogin = new SupplierLoginController;
        $status = $supplierLogin->create($request);
        //

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $response = [
            'status' => 200,
            'supplier_username' => $request->username,
            'token' => supplier_details::find($supplierID)->createToken('register_token', ['supplier'])->accessToken
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SupplierRegister  $supplierRegister
     * @return \Illuminate\Http\Response
     */
    public function show(...$supplierID)
    {
        $supplierID = collect($supplierID);
        $supplierID = $supplierID->flatten();
        $supplierDetails = supplier_details::whereIn('id', $supplierID)->get();
        $supplierIdCheck = $supplierDetails->map(function ($item) {
            return $item->id;
        });
        $status = $supplierID->diff($supplierIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 404,
                'message' => 'Data not found',
                'data' => $status->all()
            ];
            return response()->json($response, 404);
        }
        $supplierDetails = $supplierDetails->map(function ($item) {
            $item = [
                'id' => $item->id,
                'username' => $item->supplier_username,
                'fullname' => $item->supplier_fullname,
                'dob' => $item->supplier_dob,
                'address' => $item->supplier_address,
                'sex' => $item->supplier_sex == 0 ? 'female' : 'male',
                'email' => $item->supplier_email,
                'phone' => $item->supplier_phone,
                'join_date' => date_format($item->created_at, 'Y-m-d H:i:s')
            ];
            return $item;
        });
        $response = [
            'status' => 200,
            'data' => $supplierDetails
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SupplierRegister  $supplierRegister
     * @return \Illuminate\Http\Response
     */
    public function edit(SupplierRegister $supplierRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SupplierRegister  $supplierRegister
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SupplierRegister $supplierRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SupplierRegister  $supplierRegister
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierRegister $supplierRegister)
    {
        //
    }
}
