<?php

namespace App\Http\Controllers;

use App\Model\CustomerCart as customer_cart;
use App\Model\SellerShop as seller_shop;
use Illuminate\Http\Request;

class CustomerCartController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerCart $CustomerCart, Request $request)
    {
        $customerToken = $request->customerToken;
        $customerCart = customer_cart::where('customerStatus', 0)->get();
        foreach ($customerCart as $cart) {
            $sellerShopName = seller_shop::where('id', $cart->customerSellerIdShop)->first();
            $cart->customerSellerShopName = $sellerShopName;
        }

        return response()->json([$CustomerCart->all(), $customerToken], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerCart $CustomerCart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\CustomerCart  $CustomerCart
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerCart $CustomerCart)
    {
        //
    }
}
