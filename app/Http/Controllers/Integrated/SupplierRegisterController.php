<?php

namespace App\Http\Controllers\Integrated;

use App\Http\Controllers\Coin\CoinRegisterController as CoinRegister;
use App\Http\Controllers\Supplier\SupplierRegisterController as SupplierRegister;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierRegisterController extends Controller
{
    public function store(Request $request)
    {
        /* 
            Create coin account for transactions
            account type 3 a.k.a Supplier
         */

        $request->request->add(['account_type' => 2]);
        $coinDetails = new CoinRegister;
        $status = $coinDetails->store($request);
        if ($status->getStatusCode() != 200) {
            return $status;
        }

        /* 
        Create supplier account
         */
        $supplierDetails = new SupplierRegister;
        $status = $supplierDetails->store($request);
        return $status;
    }
}
