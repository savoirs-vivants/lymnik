<?php

namespace App\Http\Controllers;

use App\Models\CoursDEau;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function index()
    {
        return view('mobile.index');
    }

}
