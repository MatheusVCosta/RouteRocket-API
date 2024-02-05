<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use RedirectService;
use RedirectLogService;

class RedirectController extends Controller
{
    
    public function redirectTo(string $hash_code, Request $request)
    {
        $redirectResponse = RedirectService::redirectByHashCode($hash_code, $request->query());

        if (is_a($redirectResponse, Exception::class)) {
            return response()->json([
                'message' => $redirectResponse->getMessage(),
                'status'  => $redirectResponse->getCode(),
            ]);
        }

        $ipRequest   = $request->ip();
        $userAgent   = $request->userAgent();
        $httpReferer = $request->header('Referer', '');

        $targetUrl = $redirectResponse['redirect_to']->getTargetUrl();
        $targetUrlExplode = explode('?', $targetUrl);
        
        $redirectLogArr = [
            'redirect_id'        => $redirectResponse['redirect_id'],
            'ip_address_request' => $ipRequest,
            'user_agent'         => $userAgent,
            'header_referer'     => $httpReferer,
            'query_params'       => $targetUrlExplode[1],
            'last_access_at'     => \Carbon\Carbon::now()
        ];

        $redirectLogResponse = RedirectLogService::create($redirectLogArr);
        if (!$redirectLogResponse) {
            return $redirectLogResponse;
        };
        
        return $redirectResponse['redirect_to'];
    }
}
