<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        // ユーザー名を更新
        $user->update([
            'name' => $request->name
        ]);

        // 画像のアップロード処理
        $imgUrl = null;
        if ($request->hasFile('img_url')) {
            // 既存の画像があれば削除
            if ($user->profile && $user->profile->img_url) {
                Storage::disk('public')->delete($user->profile->img_url);
            }

            $imgUrl = $request->file('img_url')->store('profile_images', 'public');
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

        return redirect()->route('mypage');
    }
}
