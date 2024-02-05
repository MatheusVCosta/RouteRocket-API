<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RedirectService;
use RedirectLogService;

class RedirectController extends Controller
{
    
    public function redirectTo(string $hash_code, Request $request)
    {
        $redirectResponse = RedirectService::redirectByHashCode($hash_code);
    
        if (!$redirectResponse['redirect_to']) {
            return response()->json($redirectResponse);
        }

        if ($redirectResponse['status_code'] != 200) {
            
        }

        $ipRequest   = $request->ip();
        $userAgent   = $request->userAgent();
        $httpReferer = $request->header('Referer', '');
        $query       = implode($request->query());

        $redirectLogArr = [
            'redirect_id'        => $redirectResponse['redirect_id'],
            'ip_address_request' => $ipRequest,
            'user_agent'         => $userAgent,
            'header_referer'     => $httpReferer,
            'query_params'       => $query,
            'last_access_at'     => \Carbon\Carbon::now()
        ];

        RedirectLogService::create($redirectLogArr);

        return $redirectResponse['redirect_to'];
    }
}
