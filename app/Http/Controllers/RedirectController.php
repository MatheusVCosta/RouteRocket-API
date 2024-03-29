<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use RedirectService;
use RedirectLogService;

class RedirectController extends Controller
{
    /**
     * Redirect user to specified target URL
     * 
     * @method Get
     * 
     * @param string $redirect_code
     * 
     * @api /r/<redirect_code>
     * 
     * @return redirect
     */
    public function redirectTo(string $hash_code, Request $request)
    {
        $redirectResponse = RedirectService::redirectByHashCode($hash_code, $request->query());
        if (is_a($redirectResponse, Exception::class)) {
            return response()->json([
                'message' => $redirectResponse->getMessage(),
                'status'  => $redirectResponse->getCode(),
            ]);
        }

        $targetUrl = $redirectResponse['redirect_to']->getTargetUrl();
        $targetUrlExplode = explode('?', $targetUrl);
        $redirectLogArr = [
            'redirect_id'        => $redirectResponse['redirect_id'],
            'ip_address_request' => $request->ip(),
            'user_agent'         => $request->userAgent(),
            'header_referer'     => $request->header('referer'),
            'query_params'       => isset($targetUrlExplode[1]) ? $targetUrlExplode[1] : '',
            'last_access_at'     => \Carbon\Carbon::now()
        ];

        $redirectLogResponse = RedirectLogService::create($redirectLogArr);
        if (!$redirectLogResponse) {
            return $redirectLogResponse;
        };
        
        return $redirectResponse['redirect_to'];
    }
}
