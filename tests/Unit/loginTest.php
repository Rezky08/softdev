<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class loginTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function userCanLoginTest()
    {
        // $response = $this->get('/login');
        $input = ['username' => 'Rezky.setiawan85', 'password' => 'Test123#'];
        $response = $this->post('/login', $input);
        $response->assertStatus(200);
        $response->assertExactJson()('username');
    }
}
