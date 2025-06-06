<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function storeUser(RegisterRequest $request){
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        Auth::login($user);
        return redirect('/mypage/profile');
    }

    public function loginUser(LoginRequest $request){
        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)){
            return redirect('/?page=mylist');
        }

        // カスタムエラーメッセージ
        throw ValidationException::withMessages([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }
}