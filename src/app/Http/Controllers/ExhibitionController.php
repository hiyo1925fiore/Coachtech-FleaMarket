<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exhibition;

class ExhibitionController extends Controller
{
    public function getList(){
        return view('itemlist');
    }
}
