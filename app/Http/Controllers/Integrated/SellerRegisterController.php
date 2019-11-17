<?php

namespace App\Http\Controllers\Integrated;

use App\Http\Controllers\Coin\CoinRegisterController as CoinRegister;
use App\Http\Controllers\Seller\SellerRegisterController as SellerRegister;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SellerRegisterController extends Controller
{
    public function store(Request $request)
    {
        /* 
            Create coin account for transactions
            account type 2 a.k.a Seller
         */

        $request->request->add(['account_type' => 2]);
        $coinDetails = new CoinRegister;
        $status = $coinDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        /* 
        Create seller account
         */
        $sellerDetails = new SellerRegister;
        $status = $sellerDetails->store($request);
        return $status;
    }
}
