<?php

namespace App\Http\Controllers;

use App\SellerRegister;
use App\Model\SellerDetail as seller_details;
use App\Model\SellerLogin as seller_logins;
use App\Model\SellerShop as seller_shops;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class SellerRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sellerDetails = seller_details::all();
        $sellerDetails = $sellerDetails->map(function ($detail) {
            $sellerData = [
                'id' => $detail->id,
                'username' => $detail->seller_username,
                'fullname' => $detail->seller_fullname,
                'dob' => $detail->seller_dob,
                'address' => $detail->seller_address,
                'sex' => $detail->seller_sex == 0 ? 'female' : 'male',
                'email' => $detail->seller_email,
                'phone' => $detail->seller_phone,
                'join_date' => date_format($detail->created_at, 'Y-m-d H:i:s')
            ];
            return $sellerData;
        });
        $response = [
            'status' => 200,
            'data' => $sellerDetails
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
        $request->request->add(['account_type' => 2]);
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

        $sellerDetail = [
            'seller_fullname' => $request->input('fullname') ?: $request->input('username'),
            'seller_dob' => $request->input('dob'),
            'seller_address' => $request->input('address'),
            'seller_sex' => $request->input('sex'),
            'seller_email' => $request->input('email'),
            'seller_phone' => $request->input('phone'),
            'seller_username' => $request->input('username'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $sellerID = seller_details::insertGetId($sellerDetail);
        if (!$sellerID) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error',
            ];
            return response()->json($response, 500);
        }
        $request->request->add(['seller_id' => $sellerID]);

        // send to SellerLoginController to create l account
        $sellerLogin = new SellerLoginController;
        $status = $sellerLogin->create($request);
        //

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        // send to sellerShopController to add shop info
        $sellerShop = new SellerShopController;
        $status = $sellerShop->store($request);
        //

        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $response = [
            'status' => 200,
            'seller_username' => $request->username,
            'token' => seller_details::find($sellerID)->createToken('register_token', ['seller'])->accessToken
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerRegister  $sellerRegister
     * @return \Illuminate\Http\Response
     */
    public function show(...$sellerID)
    {
        $sellerID = collect($sellerID);
        $sellerID = $sellerID->flatten();
        $sellerDetails = seller_details::whereIn('id', $sellerID)->get();
        $sellerIdCheck = $sellerDetails->map(function ($item) {
            return $item->id;
        });
        $status = $sellerID->diff($sellerIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 404,
                'message' => 'Data not found',
                'data' => $status->all()
            ];
            return response()->json($response, 404);
        }
        $sellerDetails = $sellerDetails->map(function ($item) {
            $item = [
                'id' => $item->id,
                'username' => $item->seller_username,
                'fullname' => $item->seller_fullname,
                'dob' => $item->seller_dob,
                'address' => $item->seller_address,
                'sex' => $item->seller_sex == 0 ? 'female' : 'male',
                'email' => $item->seller_email,
                'phone' => $item->seller_phone,
                'join_date' => date_format($item->created_at, 'Y-m-d H:i:s')
            ];
            return $item;
        });
        $response = [
            'status' => 200,
            'data' => $sellerDetails
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SellerRegister  $sellerRegister
     * @return \Illuminate\Http\Response
     */
    public function edit(SellerRegister $sellerRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerRegister  $sellerRegister
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellerRegister $sellerRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerRegister  $sellerRegister
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerRegister $sellerRegister)
    {
        //
    }
}
