<?php

namespace App\Http\Controllers\Integrated;

use App\Http\Controllers\Coin\CoinRegisterController as CoinRegister;
use App\Http\Controllers\Customer\CustomerRegisterController as CustomerRegister;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerRegisterController extends Controller
{
    public function store(Request $request)
    {
        /* 
            Create coin account for transactions
            account type 1 a.k.a Customer
         */

        $request->request->add(['account_type' => 1]);
        $coinDetails = new CoinRegister;
        $status = $coinDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        /* 
        Create customer account
         */
        $customerDetails = new CustomerRegister;
        $status = $customerDetails->store($request);
        return $status;
    }
}
