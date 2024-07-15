<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::group(['prefix' => 'auth'], function ($router) {

//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);
// });

// Route::middleware('auth:api')->group(function () {

//     Route::post('refresh', [AuthController::class, 'refresh']);
//     Route::post('me', [AuthController::class, 'me']);
//     Route::post('logout', [AuthController::class, 'logout']);
// });

    Route::post('register', [AuthController::class, 'register']);

    Route::post('login', [AuthController::class, 'login']);

    
    Route::group([
        "middleware" => ["auth:sanctum"]
    ], function() {
        // POST route to submit data to the dashboard
        
        Route::post("create-record", [AuthController::class, "postDashboardData"]);
        // GET route to retrieve the dashboard data
        Route::get("travel-record", [AuthController::class, "getDashboardData"]);

        Route::get("logout", [AuthController::class, "logout"]);

    });


