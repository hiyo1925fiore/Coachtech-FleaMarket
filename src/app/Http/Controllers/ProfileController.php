<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getMypage(){
        return view('profile');
    }

    public function getProfile(){
        return view('profile_edit');
    }
}
