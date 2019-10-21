<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PhpParser\Node\Expr\Cast\Int_;
use Tests\TestCase;

class sellerTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function sellerCanRegister()
    {
        $input = [
            'username' => 'iniusername' . Str::random(3),
            'fullname' => 'ini nama ' . Str::random(4),
            'email' => 'iniEmail' . Str::random(4) . '@gmail.com',
            'password' => 'P@sswr0d!',
            'sex' => 0
        ];
        $response = $this->post('/api/seller/register', $input);
        $response->assertStatus(200);
    }

    /** @test */
    public function sellerCanLogin()
    {
        $input = [
            'username' => 'rezky2211977',
            'password' => 'Test123#'
        ];
        $response = $this->post('/api/seller/login', $input);
        $response->assertStatus(200);
    }

    /** @test */
    public function sellerCanAddTheirProduct()
    {
        $this->withHeaders(['Authorization' => 'aHhy7AIAvzPBdQdpB0YbaRH6N9Zw0rbhWCjenCOLhMZKSjq9tvenCidF8Y05']);
        $input = [
            'productName' => Str::random(20),
            'productPrice' => rand(1, 99999),
            'productStock' =>  rand(1, 100),
            'productImage' => UploadedFile::fake()->image('fakerImage.jpg')->size(100)
        ];
        $response = $this->post('/api/seller/1/product', $input);
        $response->assertStatus(200);
    }
}
