<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', [ExhibitionController::class, 'getList']);*/
Route::get('/', function (Request $request) {
    $page = $request->input('page');
    return view('itemlist', [
        'initial_tab' => $page === 'mylist' ? 'mylist' : 'recommended'
    ]);
})
    ->name('itemlist');
Route::get('/item/:{exhibition_id}',[ExhibitionController::class,'getDetail'])
    ->name('item.detail');
Route::post('/register', [UserController::class, 'storeUser']);
Route::post('/login', [UserController::class, 'loginUser']);

Route::middleware('auth')->group(function () {
    Route::post('/item/:{exhibition_id}',[CommentController::class,'storeComment'])
    ->name('comment.store');
    Route::post('/item/:{exhibition_id}/favorite', [FavoriteController::class, 'toggleFavorite'])->name('favorite.toggle');

    Route::get('/purchase/:{exhibition_id}', [PurchaseController::class, 'showPurchase'])
    ->name('purchase.show');
    Route::get('/purchase/address/:{exhibition_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/:{exhibition_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');
    Route::post('/purchase/:{exhibition_id}', [PurchaseController::class, 'storePurchase'])
    ->name('purchase.store');

    Route::get('/sell', [ExhibitionController::class, 'getExhibition']);
    Route::post('/sell', [ExhibitionController::class, 'storeExhibition']);

    // プロフィール関連のルート
    Route::get('/mypage', [ProfileController::class, 'getMypage'])
    ->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'getProfile'])
    ->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
});
