<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExhibitionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RatingController;
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

Route::get('/', function (Request $request) {
    $page = $request->input('page');
    return view('itemlist', [
        'initial_tab' => $page === 'mylist' ? 'mylist' : 'recommended'
    ]);
})
    ->name('itemlist');
Route::get('/item/{exhibition_id}',[ExhibitionController::class,'getDetail'])
    ->name('item.detail');
Route::post('/register', [UserController::class, 'storeUser']);
Route::post('/login', [UserController::class, 'loginUser']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // メール認証関連のルート
    // メール認証通知画面
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // メール認証処理
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/mypage/profile')->with('verified', true);
    })->middleware('signed')->name('verification.verify');

    // 認証確認ルート
    Route::get('/email/verify/check', function () {
        if (Auth::user()->hasVerifiedEmail()) {
            // 認証済み → プロフィール設定画面へ
            return redirect('/mypage/profile')->with('message', 'メール認証は完了しています。');
        } else {
            // 未認証 → そのまま認証誘導画面
            return back()->with('error', 'メール認証がまだ完了していません。メール内のリンクをクリックしてください。');
        }
    })->middleware('auth')->name('verification.check');

    // メール認証再送信
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', '認証メールを再送信しました。');
    })->middleware('throttle:6,1')->name('verification.send');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/item/{exhibitionId}',[CommentController::class,'storeComment'])
    ->name('comment.store');
    Route::post('/item/{exhibitionId}/favorite', [FavoriteController::class, 'toggleFavorite'])
    ->name('favorite.toggle');

    Route::get('/purchase/{exhibitionId}', [PurchaseController::class, 'showPurchase'])
        ->name('purchase.show');
    Route::get('/purchase/address/{exhibitionId}', [PurchaseController::class, 'editAddress'])
        ->name('purchase.address.edit');
    Route::post('/purchase/address/{exhibitionId}', [PurchaseController::class, 'updateAddress'])
        ->name('purchase.address.update');
    Route::post('/purchase/{exhibitionId}', [PurchaseController::class, 'storePurchase'])
        ->name('purchase.store');

    Route::get('/sell', [ExhibitionController::class, 'getExhibition']);
    Route::post('/sell', [ExhibitionController::class, 'storeExhibition']);

    // プロフィール関連のルート
    Route::get('/mypage', [ProfileController::class, 'getMypage'])
        ->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'getProfile'])
        ->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

    // チャット画面関連のルート
    Route::get('/chat/{exhibitionId}', [ChatController::class, 'showChat'])
        ->name('chat.show');
    Route::post('/chat/{exhibitionId}',[ChatController::class,'store'])
        ->name('chat.store');
    Route::put('/chat/{chatId}', [ChatController::class, 'update'])
        ->name('chat.update');
    Route::delete('/chat/{chatId}', [ChatController::class, 'destroy'])
        ->name('chat.destroy');
    Route::post('/chat/{exhibitionId}/close',[RatingController::class,'store'])
        ->name('rating.store');
});