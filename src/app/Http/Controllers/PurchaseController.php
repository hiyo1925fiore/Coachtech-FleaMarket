<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Exhibition;
use App\Models\Profile;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function showPurchase($exhibitionId){
        $exhibition = Exhibition::findOrFail($exhibitionId);
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

    public function editAddress($exhibitionId)
    {
        $exhibition = Exhibition::findOrFail($exhibitionId);
        $shippingAddress = session('shipping_address', [
            'post_code' => '',
            'address' => '',
            'building' => ''
        ]);

        return view('address_edit', compact('exhibition', 'shippingAddress'));
    }

    public function updateAddress(PurchaseRequest $request, $exhibitionId)
    {
        // セッションに配送先情報を保存
        session([
            'shipping_address' => [
                'post_code' => $request->post_code,
                'address' => $request->address,
                'building' => $request->building
            ]
        ]);

        return redirect()->route('purchase.show', $exhibitionId);
    }

    public function storePurchase(PurchaseRequest $request, $exhibitionId)
    {
        $exhibition = Exhibition::findOrFail($exhibitionId);
        $user = Auth::user();
        $shippingAddress = session('shipping_address');

        // purchasesテーブルに保存
        Purchase::create([
            'user_id' => $user->id,
            'exhibition_id' => $exhibitionId,
            'payment' => $request->payment,
            'post_code' => $shippingAddress['post_code'],
            'address' => $shippingAddress['address'],
            'building' => $shippingAddress['building'],
        ]);

        // セッションから配送先情報を削除
        session()->forget('shipping_address');

        return redirect()->route('itemlist',['page' => 'mylist'])
            ->with('success', '商品の購入が完了しました。発送までしばらくお待ちください。');
    }
}