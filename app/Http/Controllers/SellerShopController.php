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
                'status' => 500,
                'message' => 'Internal Server Error'
            ];
            return response()->json($response, 500);
        }
        $response = [
            'status' => 200,
            'message' => 'Shop has been created'
        ];
        return response()->json($response, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(...$id)
    {
        $id = collect($id);
        $id = $id->flatten();
        $shops = seller_shops::find($id);
        $shops = $shops->map(function ($item) {
            $seller = $item->seller;
            $seller = (object) [
                'id' => $seller->id,
                'username' => $seller->seller_username,
                'fullname' => $seller->seller_fullname,
                'dob' => $seller->seller_dob,
                'address' => $seller->seller_address,
                'sex' => $seller->seller_sex == 0 ? 'female' : 'male',
                'email' => $seller->seller_email,
                'phone' => $seller->seller_phone,
                'join_date' => date_format($seller->created_at, 'Y-m-d H:i:s')
            ];
            $item = (object) [
                'id' => $item->id,
                'seller_id' => $item->seller_id,
                'shop_name' => $item->seller_shop_name,
                'shop_owner_name' => $item->seller_shop_owner_name,
                'shop_phone' => $item->seller_shop_phone,
                'shop_certificate' => $item->seller_shop_certificate,
                'join_date' => $item->created_at,
                'seller' => $seller
            ];
            return $item;
        });
        $shopIdCheck = $shops->map(function ($item) {
            return $item->id;
        });
        $status = $id->diff($shopIdCheck);
        if (!$status->isEmpty()) {
            $response = [
                'status' => 400,
                'message' => 'sorry, shop not found',
                'data' => $status->all()
            ];
            return response()->json($response, 400);
        }

        $response = [
            'status' => 200,
            'data' => $shops
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
