<?php

namespace App\Http\Controllers;

use App\Model\SellerShop as seller_shops;
use Illuminate\Http\Request;

class SellerShopController extends Controller
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
        $sellerID = $request->seller_id;
        $sellerShop = [
            'seller_id' => $sellerID,
            'seller_shop_name' => $request->input('username'),
            'seller_shop_owner_name' => $request->input('fullname') ?: $request->input('username'),
            'seller_shop_phone' => $request->input('phone'),
            'seller_shop_certificate' => $request->input('shop_certificate'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];

        $status = seller_shops::insert($sellerShop);
        if (!$status) {
            $response = [
                'status'=> 500,
                'message'=>'Internal Server Error'
            ];
            return response()->json($response,500);
        }
        $response = [
            'status'=> 200,
            'message'=>'Shop has been created'
        ];
        return response()->json($response,200);

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
