<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class customerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function customerCanRegister()
    {
        $input = [
            'username' => 'iniusername' . Str::random(3),
            'fullname' => 'ini nama ' . Str::random(4),
            'email' => 'iniEmail' . Str::random(4) . '@gmail.com',
            'password' => 'P@sswr0d!',
            'sex' => 0
        ];
        $response = $this->post('/api/customer/register', $input);
        $response->assertStatus(200);
    }

    /** @test */
    public function customerCanLogin()
    {
        $input = [
            'username' => 'rezky221197',
            'password' => 'Test123#'
        ];
        $response = $this->post('/api/customer/login', $input);
        $response->assertStatus(200);
    }
}
