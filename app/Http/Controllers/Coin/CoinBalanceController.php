<?php

namespace App\Http\Controllers\Coin;

use App\Http\Controllers\Controller;


use App\Model\CoinBalance as coin_balances;
use App\Model\CoinDetail as coin_details;
use Illuminate\Http\Request;

class CoinBalanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $coin_balance = $request->coin_balance;
        $status = coin_balances::insert($coin_balance->toArray());
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status' => 200,
            'message' => 'Account balance created'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function validateBalance($username, $balance)
    {
        $coinData = coin_details::where('coin_details.coin_username', $username)->rightJoin('coin_balances', 'coin_details.id', '=', 'coin_balances.coin_id')->get()->first();
        $currentBalance = $coinData->coin_balance - $balance;
        if ($currentBalance < 0) {
            $response = [
                'status' => 400,
                'message' => 'The ballance is not sufficent'
            ];
            return response()->json($response, 400);
        }

        $response = [
            'status' => 200,
            'message' => 'sufficent balance'
        ];
        return response()->json($response, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function balanceDebit($coinId, $balance)
    {
        $whereCond = [
            'coin_id' => $coinId
        ];
        $coinBalance = coin_balances::where($whereCond)->first();
        $currentBalance = $coinBalance->coin_balance - $balance;
        if ($currentBalance < 0) {
            $response = [
                'status' => 400,
                'message' => 'The ballance is not sufficent'
            ];
            return response()->json($response, 400);
        }

        $status = coin_balances::where($whereCond)->update(['coin_balance' => $currentBalance]);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Balance has been updated'
        ];
        return response()->json($response, 200);
    }

    public function balanceCredit($coinId, $balance)
    {
        $whereCond = [
            'coin_id' => $coinId
        ];
        $coinBalance = coin_balances::where($whereCond)->get()->first();
        $currentBalance = $coinBalance->coin_balance + $balance;

        $status = coin_balances::where($whereCond)->update(['coin_balance' => $currentBalance]);
        if (!$status) {
            $response = [
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }

        $response = [
            'status' => 200,
            'message' => 'Balance has been updated'
        ];
        return response()->json($response, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
