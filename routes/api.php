<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ResetPasswordController;
use Illuminate\Auth\Events\Logout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
Route::post('/auth/google', [GoogleAuthController::class,'loginWithGoogle']);

Route::post('/sendOTP',[ResetPasswordController::class,'sendOTP']);
Route::post('/verify',[ResetPasswordController::class,'verifyOtp']);
Route::put('/resetPassword',[ResetPasswordController::class,'resetPassword']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::post('/add-favorite/{itemId}', [FavoriteController::class, 'addFavorite']);
    Route::get('/show-favorite', [FavoriteController::class, 'showFavorites']);
    Route::delete('/remove-favorite/{itemId}', [FavoriteController::class, 'removeFavorite']);
    // Route::get('/notifications',[NotificationController::class,'index']);
    // Route::post('/notifications/{id}/read',[NotificationController::class,'index']);
    Route::post('/cart/add',[CartController::class,'addToCart']);
    Route::get('/history/order',[CartController::class,'history']);
    Route::post('/orders/{orderId}/reorder', [CartController::class, 'reorder']);
    Route::get('/order/history/details/{orderId}',[CartController::class,'historyDetailsWithOptions']);
    Route::get('/cart/items',[CartController::class,'getCartItems']);
    Route::delete('/cart/delete/{cartId}',[CartController::class,'deleteCartItem']);
    Route::put('/cart/reduce-one',[CartController::class,'reduceQuantityByOne']);
    Route::put('/cart/increase-one',[CartController::class,'increaseQuantityByOne']);
});

Route::get('/categories',[CategoryController::class,'index']);
Route::get('/topRated/{id}',[ItemController::class,'topRated']);
Route::get('/Recommended/{id}',[ItemController::class,'Recommended']);
Route::get('/Offers',[DiscountController::class,'offers']);
// Route::get('/FilterPage',[FilterController::class,'dataForFilterPage']);
Route::get('/Item-Details/{id}',[ItemController::class,'show']);
Route::get('/ItemsCategory/{id}',[CategoryController::class,'ItemsCategory']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/notifications/send', [NotificationController::class, 'sendNotifications']);
    Route::get('/notifications', [NotificationController::class, 'getUserNotification']);
    Route::get('/notifications/unread', [NotificationController::class, 'getUserUnreadNotifications']);
    Route::get('/notifications/read', [NotificationController::class, 'getUserReadNotifications']);
    Route::post('/notifications/read/{id}', [NotificationController::class, 'markNotificationAsRead']);
});
