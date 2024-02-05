<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRedirectRequest;
use App\Http\Requests\UpdateRedirectRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use RedirectService;

class CrudRedirectController extends Controller
{
    /**
     * Api to fetch all redirects registered
     * 
     * @method GET
     * 
     * @api /api/redirect
     * 
     * @return array
     */
    public function index() 
    {
        return RedirectService::findAll();
    }

    /**
     * Api to create a new redirect
     * 
     * @method POST
     * 
     * @param Request $request
     * 
     * @api /api/redirect
     * 
     * @return bool
     */
    public function create(CreateRedirectRequest $request)
    {
        $params  = $request->all();
        $message = "Redirect Created with success";
        $status  = 200;
        
        if ($request->fullUrl() == $params['url_target']) {
            return response()->json([
                'message' => "URL de destino não pode ser a mesma da URL origem", 
                'status'  => 400
            ]);
        }

        $redirectResponse = RedirectService::create($params);
        
        if (is_a($redirectResponse, Exception::class)) {
            $message = $redirectResponse->getMessage();
            $status  = $redirectResponse->getCode();
        }

        return response()->json([
            'message' => $message, 
            'status'  => $status
        ]);
    }

    /**
     * Api to update a redirect
     * 
     * @method PUT
     * 
     * @param Request $request
     * 
     * @api /api/redirect/<redirect_code>
     * 
     * @return bool
     */
    public function update(string $redirect_code, UpdateRedirectRequest $request)
    {
        $params = $request->all();
        $redirectResponse = RedirectService::update($redirect_code, $params);
        if (is_a($redirectResponse, Exception::class)) {
            return response()->json([
                'message' => $redirectResponse->getMessage(),
                'status'  => $redirectResponse->getCode(),
            ]);
        }
        return response()->json([
            'message' => 'Redirect updated with success', 
            'status'  => 200
        ]);
    }

    /**
     * Api to delete and disable a redirect
     * 
     * @method DELETE
     * 
     * @param string $redirect_code
     * 
     * @api /api/redirect/<redirect_code>
     * 
     * @return bool
     */
    public function delete(string $redirect_code)
    {
        $redirectResponse = RedirectService::delete($redirect_code);
        if (is_a($redirectResponse, Exception::class)) {
            return response()->json([
                'message' => $redirectResponse->getMessage(),
                'status'  => $redirectResponse->getCode(),
            ]);
        }
        return response()->json(['message' => 'Redirect deleted with success', 200]);
    }

    /**
     * Api to list Statics in the last 10 days
     * 
     * @method Get
     * 
     * @param string $redirect_code
     * 
     * @api /api/redirect/<redirect_code>/stats
     * 
     * @return json
     */
    public function stats(string $redirect_code) 
    {
        $redirectStats = RedirectService::getRedirectStats($redirect_code);
        if (empty($redirectStats)) {
            return response()->json(['message' => "Redirect don't have access to show statistics"], 400);
        }
        return response()->json($redirectStats, 200);
    }

    /**
     * Api to list logs about redirect by $redirect_code
     * 
     * @method GET
     * 
     * @param string $redirect_code
     * 
     * @api /api/redirect/redirect_code/logs
     * 
     * @return json
     */
    public function logs(string $redirect_code) 
    {
        $redirectLogs = RedirectService::getRedirectLogs($redirect_code);
        if (empty($redirectLogs)) {
            return response()->json(['message' => "Redirect don't have logs"], 400);
        }
        return response()->json([
            'total' => $redirectLogs->count(),
            'data'  => $redirectLogs
        ], 200);
    }
}
