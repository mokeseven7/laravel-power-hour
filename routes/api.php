<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

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

//Token issuing endpoint, this requires a user with basic auth credentials. 
//Once you get a token with the employee scopes, you can send that as a bearer token
//I made a command for the user generation, php artisan user:create, it will prompt you
//Make sure to note the user/pass you enter. 
Route::middleware('stateless.basic')->post('/tokens/employee', [EmployeeTokenController::class, 'issue']);

// Employee Resource CRUD Actions (yes i have a reason im not going to use the resource class)
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::get('{employee}', [EmployeeController::class, 'show']);
    Route::put('{employee}', [EmployeeController::class, 'update']);
    Route::delete('{employee}', [EmployeeController::class, 'destroy']);
});
