<?php

use App\Http\Controllers\ClassMakerWebhook\ClassMackerApiWebhookController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/', function () {
    return new JsonResponse(['data' => "test"], 200);
});

Route::any('/classmaker-webhook', [ClassMackerApiWebhookController::class, 'webHook']);
