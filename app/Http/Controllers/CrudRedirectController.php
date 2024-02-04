<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use CrudRedirectService;

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
        return CrudRedirectService::findAll();
    }

    public function create(Request $request)
    {
        $params = $request->all();
        return CrudRedirectService::create($params);
    }

    public function update(string $redirect_code, Request $request)
    {
        $params = $request->all();
        return CrudRedirectService::update($redirect_code, $params);
    }

    public function delete(string $redirect_code)
    {
        return CrudRedirectService::delete($redirect_code);
    }
}
