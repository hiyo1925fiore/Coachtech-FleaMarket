<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getMypage(){
        return view('profile');
    }

    public function getProfile(){
        $user=Auth::user();
        $profile = Profile::find($user->id);
        return view('profile_edit', compact('user', 'profile'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        // 既存の画像ファイル名を保存
        $oldImageName = $user->profile_image;

        // ユーザー名を更新
        $user->update([
            'name' => $request->name
        ]);

        // 画像がアップロードされた場合の処理
        $imgUrl = null;
        if ($request->hasFile('img_url')) {
            // 既存の画像ファイルを削除
            if ($oldImageName && Storage::disk('public')->exists('profile_images/' . $oldImageName)) {
                Storage::disk('public')->delete('profile_images/' . $oldImageName);
            }

            // 新しい画像を保存
            $image = $request->file('img_url');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('profile_images', $imageName, 'public');
            $imgUrl =  'profile_images/'. $imageName;
        } elseif ($user->profile) {
            $imgUrl = $user->profile->img_url;
        }

        // プロフィールを更新または作成
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'img_url' => $imgUrl,
                'post_code' => $request->post_code,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('itemlist',['page' => 'mylist']);
    }
}
