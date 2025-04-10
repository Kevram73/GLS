<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\PointOfSaleController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\TypeUserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PlainteController;


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

Route::get('/', [AuthController::class, 'index']);
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('otp-verify', [AuthController::class, 'otpVerify']);
Route::post('add/client', [UserController::class, 'add_client']);
Route::get('check/email/{email}', [UserController::class, 'check_email_availability']);
Route::get('check/phone/{num_phone}', [UserController::class, 'check_phone_availability']);
Route::get('/users/type/{type_user_id}', [UserController::class, 'getUsersByType']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('dashboard', [HomeController::class, 'dashboard']);
    Route::post('change-user-image', [HomeController::class, 'change_user_image']);
    Route::post('change-password', [HomeController::class, 'change_password']);
    Route::get('users/no-pos', [HomeController::class, 'get_users']);


    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/update', [UserController::class, 'update']);
    Route::delete('user/delete', [UserController::class, 'destroy']);

    // Liste des utilisateurs par type
    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'create']);
    Route::get('users/{id}', [UserController::class, 'show']);


    Route::post('messages/send', [MessageController::class, 'sendMessage']);
    Route::get('messages/conversation/{otherUserId}', [MessageController::class, 'getMessages']);
    Route::delete('messages/{messageId}', [MessageController::class, 'deleteMessage']);
    Route::get('messages/contacted-users', [MessageController::class, 'getUsersContacted']);

    Route::get('ventes', [VenteController::class, 'index']);
    Route::post('ventes', [VenteController::class, 'store']);
    Route::get('ventes/{id}', [VenteController::class, 'show']);
    Route::put('ventes/{id}', [VenteController::class, 'update']);
    Route::delete('ventes/{id}', [VenteController::class, 'destroy']);

    Route::get('ventes/seller/{sellerId}', [VenteController::class, 'salesBySeller']);
    Route::get('ventes/point-of-sale/{pointOfSaleId}', [VenteController::class, 'salesByPointOfSale']);
    Route::get('ventes/unpaid', [VenteController::class, 'unpaidSales']);

    Route::get('point-of-sales', [PointOfSaleController::class, 'index']);
    Route::post('point-of-sales', [PointOfSaleController::class, 'store']);
    Route::get('point-of-sales/{id}', [PointOfSaleController::class, 'show']);
    Route::put('point-of-sales/{id}', [PointOfSaleController::class, 'update']);
    Route::delete('point-of-sales/{id}', [PointOfSaleController::class, 'destroy']);


    Route::get('point-of-sales/active', [PointOfSaleController::class, 'activePoints']);
    Route::put('point-of-sales/{id}/activate', [PointOfSaleController::class, 'activate_point']);
    Route::put('point-of-sales/{id}/deactivate', [PointOfSaleController::class, 'deactivate_point']);


    Route::get('notifications', [NotificationController::class, 'index']);
    Route::post('notifications', [NotificationController::class, 'store']);
    Route::get('notifications/{id}', [NotificationController::class, 'show']);
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::get('notifications/unread', [NotificationController::class, 'unreadNotifications']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);

    Route::get('journals', [JournalController::class, 'index']);
    Route::post('journals', [JournalController::class, 'store']);
    Route::get('journals/{id}', [JournalController::class, 'show']);
    Route::put('journals/{id}', [JournalController::class, 'update']);
    Route::delete('journals/{id}', [JournalController::class, 'destroy']);

    Route::get('journals/active', [JournalController::class, 'activeJournals']);

    Route::get('type-users', [TypeUserController::class, 'index']);
    Route::get('type-users/others', [TypeUserController::class, 'get_other_types']);
    Route::post('type-users', [TypeUserController::class, 'store']);
    Route::get('type-users/{id}', [TypeUserController::class, 'show']);
    Route::put('type-users/{id}', [TypeUserController::class, 'update']);
    Route::delete('type-users/{id}', [TypeUserController::class, 'destroy']);
    Route::apiResource('plaintes', PlainteController::class);

});
