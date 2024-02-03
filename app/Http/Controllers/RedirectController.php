<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RedirectService;

class RedirectController extends Controller
{
    /**
     * Api to fetch all redirect registered
     * 
     * @method GET
     * 
     * @api /api/redirect
     * 
     * @return array
     */
    public function index() 
    {
        return RedirectService::teste();
    }

    public function create()
    {

    }
}
