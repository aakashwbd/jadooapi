<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BasicInfoController;

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

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout']);
Route::post('/auth/contact', [AuthController::class, 'contactMsg']);

Route::get('/auth/me', [AuthController::class, 'me']);
Route::get('/auth/user/{id}', [AuthController::class, 'show']);
Route::patch('/auth/update', [AuthController::class, 'update']);

// Route::post('/information/basic', [BasicInfoController::class, 'store']);
// Route::get('/information/basic', [BasicInfoController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
