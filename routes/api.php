<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('me', [AuthController::class, 'me']);

    // admin-only endpoints
    Route::middleware('role:admin')->group(function () {
        Route::get('users', [AuthController::class, 'index']);
        Route::patch('users/{user}/role', [AuthController::class, 'assignRole']);
    });
});