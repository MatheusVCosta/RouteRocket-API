<?php

use App\Http\Controllers\CrudRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(CrudRedirectController::class)->group(function ($api) {
    $api->get('/redirect', 'index');
    $api->post('/redirect', 'create');
    $api->put('/redirect/{redirect_code}', 'update');
    $api->delete('/redirect/{redirect_code}', 'delete');

    $api->get('/redirect/{redirect_code}/stats', 'stats');
    $api->get('/redirect/{redirect_code}/logs', 'logs');
});