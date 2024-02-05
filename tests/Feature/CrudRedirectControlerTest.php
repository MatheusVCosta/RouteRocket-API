<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Database\Factories\RedirectFactory;
use GuzzleHttp\Psr7\Header;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CrudRedirectControlerTest extends TestCase
{   
    use RefreshDatabase;
    use WithFaker;

    const URL_PATH_REDIRECT = "/api/redirect";

    /**
     * Test to checked if request its ok
     *
     * @return void
     */

    public function test_create_redirect_success()
    {
        $redirectFaker = Redirect::factory()->make(
            ['url_target' => "https://www.teste.com"]
        );
        
        $response = $this->post(self::URL_PATH_REDIRECT, [
            'url_target' => $redirectFaker['url_target']
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message', 
                'status'
            ])
            ->assertJson([
                'message' => 'Redirect Created with success', 
                'status' => 200
            ]);
    }

    /**
     * Test to checked if dns is invalid
     *
     * @return void
     */
    public function test_create_redirect_error_dns_invalid()
    {
        $invalidDNS = "https://umdominiomuitolongoqueprovavelmenteseráinvalido.com";
        $redirectFaker = Redirect::factory()->make();
        $response = $this->post(self::URL_PATH_REDIRECT, [
            'url_target' => $invalidDNS
        ]);
        
        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'url_target'
                ], 
            ])
            ->assertJson([
                'errors' => [
                    'url_target' => [
                        'O DNS fornecido não é válido.'
                    ]
                ]
            ]);
    }

    /**
     * Test to checked if url is without https
     *
     * @return void
     */
    public function test_create_redirect_error_without_https()
    {
        $invalidUrl = "www.teste.com";
        $redirectFaker = Redirect::factory()->make();
        $response = $this->post(self::URL_PATH_REDIRECT, [
            'url_target' => $invalidUrl
        ]);
        
        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    'url_target'
                ], 
            ])
            ->assertJson([
                'errors' => [
                    'url_target' => [
                        'A URL informada precisa ter o protocolo HTTPS.'
                    ]
                ]
            ]);
    }

}
