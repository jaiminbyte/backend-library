<?php

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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\OrganizationRoomController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AccessRightController;

Route::post('register', [AuthController::class, 'register']);
Route::post('user_register', [AuthController::class, 'user_register']);
Route::post('google_register', [AuthController::class, 'google_register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::middleware('jwt.verify')->group(function () {
    Route::get('data', [BookController::class, 'data']);

    Route::get('users', [AuthController::class, 'users']);

    Route::get('get_modules', [ModuleController::class, 'get_modules']);

    Route::apiResource('customers', CustomerController::class);

    Route::apiResource('workspaces', WorkspaceController::class);

    Route::apiResource('facilities', FacilityController::class);

    Route::apiResource('conferences', ConferenceController::class);

    Route::get('booking_get', [BookingController::class,'booking_get']);
    Route::post('booking_save', [BookingController::class,'booking_save']);

    Route::apiResource('organizations', OrganizationController::class);

    Route::apiResource('organization_rooms', OrganizationRoomController::class);

    Route::apiResource('books', BookController::class);

    Route::post('search_google_api', [BookController::class, 'search_google_api']);
    Route::get('latestbooks', [BookController::class, 'latestbooks']);
    Route::get('bestseller', [BookController::class, 'bestseller']);
    Route::get('toprated', [BookController::class, 'toprated']);

    Route::get('dashboard', [BookingController::class, 'dashboard']);

    Route::apiResource('access-rights', AccessRightController::class);

    Route::get('history', [BookingController::class, 'history']);
});
