<?php

namespace App\Http\Controllers;

use App\CoinRegister;
use App\Model\CoinDetail as coin_details;
use App\Model\CoinLogin as coin_logins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;

class CoinRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coinData = [];
        $coinDetails = coin_details::all();
        foreach ($coinDetails as $coin => $detail) {
            $coinData[] = [
                'id' => $detail->id,
                'username' => $detail->coin_username,
                'fullname' => $detail->coin_fullname,
                'dob' => $detail->coin_dob,
                'address' => $detail->coin_address,
                'sex' => $detail->coin_sex == 0 ? 'female' : 'male',
                'email' => $detail->coin_email,
                'phone' => $detail->coin_phone,
                'join_date' => date_format($detail->created_at, 'Y-m-d H:i:s')
            ];
        }
        $response = [
            'status' => 200,
            'data' => $coinData
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
            'username' => ['required', 'unique:dbmarketcoins.coin_logins,coin_username'],
            'password' => ['required', 'min:8', 'max:12', 'regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?=.*[\d]).{8,12}$/'],
            'email' => ['required', 'email', 'unique:dbmarketcoins.coin_details,coin_email']
        ]);
        if ($validation->fails()) {
            $response = [
                'status' => 401,
                'message' => $validation->errors()
            ];
            return response()->json($response, 401);
        }

        $coinDetail = [
            'coin_fullname' => $request->input('fullname') ?: $request->input('username'),
            'coin_dob' => $request->input('dob'),
            'coin_address' => $request->input('address'),
            'coin_sex' => $request->input('sex'),
            'coin_email' => $request->input('email'),
            'coin_phone' => $request->input('phone'),
            'coin_username' => $request->input('username'),
            'coin_account_type' => $request->input('account_type'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $coinID = coin_details::insertGetId($coinDetail);
        $coinLogin = [
            'coin_username' => $request->input('username'),
            'coin_password' => Hash::make($request->input('password')),
            'coin_status' => 1,
            'coin_id' => $coinID,
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
        $status = coin_logins::insert($coinLogin);


        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        // create account balance
        $coinLogin = collect($coinLogin);
        $coinLogin = $coinLogin->except(['coin_status', 'coin_password']);
        $request->request->add(['coin_balance' => $coinLogin]);
        $coinBalance = new CoinBalanceController;
        $status = $coinBalance->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        $response = [
            'status' => 200,
            'coin_username' => $coinLogin['coin_username'],
            'token' => coin_details::find($coinID)->createToken('register_token', ['coin'])->accessToken
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CoinRegister  $coinRegister
     * @return \Illuminate\Http\Response
     */
    public function show($coinID)
    {
        $coinDetails = coin_details::where('id', $coinID);
        if (!$coinDetails->exists()) {
            $response = [
                'status' => 404,
                'message' => 'Data not found'
            ];
            return response()->json($response, 404);
        }
        $coinDetails = $coinDetails->first();
        $coinDetails = [
            'id' => $coinDetails->id,
            'username' => $coinDetails->coin_username,
            'fullname' => $coinDetails->coin_fullname,
            'dob' => $coinDetails->coin_dob,
            'address' => $coinDetails->coin_address,
            'sex' => $coinDetails->coin_sex == 0 ? 'female' : 'male',
            'email' => $coinDetails->coin_email,
            'phone' => $coinDetails->coin_phone,
            'join_date' => date_format($coinDetails->created_at, 'Y-m-d H:i:s')
        ];
        $response = [
            'status' => 200,
            'data' => $coinDetails
        ];
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CoinRegister  $coinRegister
     * @return \Illuminate\Http\Response
     */
    public function edit(CoinRegister $coinRegister)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CoinRegister  $coinRegister
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoinRegister $coinRegister)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CoinRegister  $coinRegister
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoinRegister $coinRegister)
    {
        //
    }
}
