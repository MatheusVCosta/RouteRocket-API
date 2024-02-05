<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
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

    public function create(Request $request)
    {
        $params = $request->all();
        return RedirectService::create($params);
    }

    public function update(string $redirect_code, Request $request)
    {
        $params = $request->all();
        return RedirectService::update($redirect_code, $params);
    }

    public function delete(string $redirect_code)
    {
        return RedirectService::delete($redirect_code);
    }
}
