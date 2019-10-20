<?php

namespace App\Http\Controllers;

// use App\CustomerLogin;
use Illuminate\Http\Request;
use App\Model\CustomerLogin as customer_login;
use App\Model\CustomerRegister as customer_register;
use App\Model\CustomerSecurity as customer_security;
use DateTime;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerLoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $username_input = $request->input('username');
        $password_input = $request->input('password');
        $username_input = 'Rezky.setiawan85';
        $password_input = 'Test123#';
        $customerLogin  = customer_register::where('customerUsername', $username_input);
        if ($customerLogin->exists()) {
            $customerLogin = $customerLogin->first();
            $password = customer_security::where('username', $username_input)->first()->password;
            $password = Hash::make($password);
            if (Hash::check($password_input, $password)) {
                $loginInfo = [
                    'username' => $customerLogin->customerUsername,
                    'LoginTime' => date_format(now(), 'Y-m-d H:i:s')
                ];
                $request->session()->put($loginInfo);
                // Redirect to home or last page
                return redirect('/');
            }
            // Redirect to login page again
            return redirect('/');
        }
        // Redirect to login page again
        return redirect('/');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CustomerLogin $customerLogin)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CustomerLogin  $customerLogin
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerLogin $customerLogin)
    {
        //
    }
}
