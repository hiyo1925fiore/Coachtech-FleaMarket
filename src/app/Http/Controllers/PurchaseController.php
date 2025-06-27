<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Exhibition;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function showPurchase($exhibition_id){
        $exhibition = Exhibition::findOrFail($exhibition_id);
        $user=Auth::user();

        // セッションから配送先情報を取得、なければprofilesテーブルから初期値を設定
        if (!session('shipping_address')) {
            $profile = $user->profile;
            session([
                'shipping_address' => [
                    'post_code' => $profile->post_code ?? '',
                    'address' => $profile->address ?? '',
                    'building' => $profile->building ?? ''
                ]
            ]);
        }

        $shippingAddress = session('shipping_address');

        return view('purchase',compact('exhibition', 'user', 'shippingAddress'));
    }

    public function editAddress($exhibition_id)
    {
        $exhibition = Exhibition::findOrFail($exhibition_id);
        $shippingAddress = session('shipping_address', [
            'post_code' => '',
            'address' => '',
            'building' => ''
        ]);

        return view('address_edit', compact('exhibition', 'shippingAddress'));
    }

    public function updateAddress(PurchaseRequest $request, $exhibition_id)
    {
        // セッションに配送先情報を保存
        session([
            'shipping_address' => [
                'post_code' => $request->post_code,
                'address' => $request->address,
                'building' => $request->building
            ]
        ]);

        return redirect()->route('purchase.show', $exhibition_id);
    }

    public function storePurchase(PurchaseRequest $request, $exhibition_id)
    {
        $exhibition = Exhibition::findOrFail($exhibition_id);
        $user = Auth::user();
        $shippingAddress = session('shipping_address');

        // purchasesテーブルに保存
        Purchase::create([
            'user_id' => $user->id,
            'exhibition_id' => $exhibition_id,
            'payment' => $request->payment,
            'post_code' => $shippingAddress['post_code'],
            'address' => $shippingAddress['address'],
            'building' => $shippingAddress['building'],
        ]);

        // セッションから配送先情報を削除
        session()->forget('shipping_address');

        return redirect()->route('itemlist');
    }
}