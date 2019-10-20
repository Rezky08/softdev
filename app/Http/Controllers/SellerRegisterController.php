<?php

namespace App\Http\Controllers;

use App\SellerRegister;
use App\Model\SellerDetail as seller_details;
use App\Model\SellerLogin as seller_logins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SellerRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('register');
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
        $accountExist = seller_details::where('sellerUsername', $request->input('username'))->exists();
        if ($accountExist) {
            $response = [
                'status' => 401,
                'message' => 'Account was exists'
            ];
            return response()->json($response, 401);
        }
        $sellerDetail = [
            'sellerFullname' => $request->input('fullname'),
            'sellerDOB' => $request->input('DOB'),
            'sellerAddress' => $request->input('address'),
            'sellerSex' => $request->input('sex'),
            'sellerEmail' => $request->input('email'),
            'sellerPhone' => $request->input('phone'),
            'sellerUsername' => $request->input('username'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $sellerID = seller_details::insertGetId($sellerDetail);
        $sellerLogin = [
            'sellerUsername' => $request->input('username'),
            'sellerPassword' => Hash::make($request->input('password')),
            'sellerToken' => Str::random(60),
            'sellerStatus' => 1,
            'sellerId' => $sellerID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = seller_logins::insert($sellerLogin);
        if ($status) {
            $response = [
                'status' => 200,
                'sellerUsername' => $sellerLogin['sellerUsername'],
                'token' => $sellerLogin['sellerToken']
            ];
            return response()->json($response, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerRegister  $sellerRegister
     * @return \Illuminate\Http\Response
     */
    public function show(SellerRegister $sellerRegister)
    {
        //
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
