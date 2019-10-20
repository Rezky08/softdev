<?php

namespace App\Http\Controllers;

use App\Model\SellerProduct;
use Illuminate\Http\Request;

class SellerProductController extends Controller
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
        $productDetail = [
            'sellerProductName' => $request->input('productName'),
            'sellerProductPrice' => $request->input('productPrice'),
            'sellerProductStock' => $request->input('productStock'),
            'created_at' => date_format(now(), 'Y-m-d H:i:s'),
            'updated_at' => date_format(now(), 'Y-m-d H:i:s')
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function show(SellerProduct $sellerProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SellerProduct $sellerProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SellerProduct  $sellerProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(SellerProduct $sellerProduct)
    {
        //
    }
}
