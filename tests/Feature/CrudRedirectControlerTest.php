<?php

namespace Tests\Feature;

use App\Models\Redirect;
use App\Models\RedirectLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Vinkla\Hashids\Facades\Hashids;
use RedirectService;

class CrudRedirectControlerTest extends TestCase
{   
    use RefreshDatabase;
    use WithFaker;

    const URL_PATH_REDIRECT = "/api/redirect/";

    /**
     * Test to checked if request its ok
     *
     * @return void
     */

    public function test_create_redirect_success()
    {
        $redirectFaker = Redirect::factory()->make(
            ['url_target' => "https://www.google.com"]
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
     * Test to generate a redirect
     *
     * @return void
     */
    public function test_generate_redirect_url()
    {
        $targetUrl = 'https://teste.com?utm_campaign=ads';
        $queryParams = ['utm_source' => 'facebook'];
        $resultUrl = RedirectService::generateTargetUrl($queryParams, $targetUrl);
        $this->assertEquals(
            'https://teste.com?utm_source=facebook&utm_campaign=ads', 
            $resultUrl
        );
    }

    /**
     * Test to generate a redirect without params
     *
     * @return void
     */
    public function test_generate_redirect_url_without_params()
    {
        $targetUrl = 'https://teste.com?utm_source=facebook';
        $queryParams = ['utm_source' => '', 'utm_campaign' => 'test'];
        $resultUrl = RedirectService::generateTargetUrl($queryParams, $targetUrl);
        $this->assertEquals('https://teste.com?utm_source=facebook&utm_campaign=test', $resultUrl);
    }

    /**
     * Test to generate a redirect with priority params
     *
     * @return void
     */
    public function test_generate_redirect_url_with_priority_params()
    {
        $targetUrl = 'https://teste.com?utm_source=facebook&utm_campaign=ads';
        $queryParams = ['utm_source' => 'instagram'];
        $resultUrl = RedirectService::generateTargetUrl($queryParams, $targetUrl);
        $this->assertEquals('https://teste.com?utm_source=instagram&utm_campaign=ads', $resultUrl);
    }
    /**
     * Test to checked if url is without https
     *
     * @return void
     */
    public function test_create_redirect_error_without_https()
    {
        $invalidUrl = "www.google.com";
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


    /**
     * Test to list stats with top referer
     *
     * @return void
     */
    public function test_stats_top_referer_ok()
    {
        $redirect = Redirect::factory()->create();
        $code     = Hashids::encode($redirect->id);
        $redirect->code = $code;

        $referer1 = "https://www.site.com";
        $referer2 = "https://www.site2.com";
        
        RedirectLog::factory()->create([
            'redirect_id'    => $redirect->id,
            'header_referer' => $referer1
        ]);
        RedirectLog::factory()->create([
            'redirect_id'    => $redirect->id,
            'header_referer' => $referer1
        ]);
        RedirectLog::factory()->create([
            'redirect_id'    => $redirect->id,
            'header_referer' => $referer2
        ]);
        
        $response = $this->get(self::URL_PATH_REDIRECT . $code . '/stats');
        $response->assertJson([
            'topReferers' => [
                [
                    'header_referer' => $referer1,
                    'top_referers'   => 2,
                ],
                [
                    'header_referer' => $referer2,
                    'top_referers'   => 1
                ],
            ]
           
        ]);
    }

     /**
     * Test to list stats in the last 10 days
     *
     * @return void
     */
    public function test_stats_last_10_days()
    {
        $redirect = Redirect::factory()->create();
        $code = HashIds::encode($redirect->id);

        $amountAccess = rand(1,9);
        $log = RedirectLog::factory($amountAccess)->create([
            'redirect_id'    => $redirect->id,
            'last_access_at' => Carbon::now()->subDays(rand(1,9)),
        ]);

        $response = $this->get(self::URL_PATH_REDIRECT .$code.'/stats');
        $this->assertEquals($amountAccess, $response->json()['total']);
    }

    /**
     * Test to list stats in the last 10 days but withou access
     * 
     * @return void
     */
    public function test_stats_last_10_days_no_access()
    {
        $redirect = Redirect::factory()->create();
        $code = HashIds::encode($redirect->id);
        $response = $this->get(self::URL_PATH_REDIRECT .$code.'/stats');
        $this->assertEquals($response->json()['message'], "Redirect don't have access to show statistics");
    }

    /**
     * test to check if the filter for the last 10 days is ok
     * 
     * @return void
     */
    public function test_stats_last_10_days_with_30_days()
    {
        $redirect = Redirect::factory()->create();
        $code     = HashIds::encode($redirect->id);

        RedirectLog::factory(2)->create([
            'redirect_id'    => $redirect->id,
            'last_access_at' => Carbon::now()->subDays(30),
        ]);

        RedirectLog::factory(1)->create([
            'redirect_id'    => $redirect->id,
            'last_access_at' => Carbon::now()->subDays(rand(1,9)),
        ]);

        $response = $this->get(self::URL_PATH_REDIRECT .$code.'/stats');
        $this->assertEquals(1, $response->json()['last_ten_days']['total']);
    }
}
