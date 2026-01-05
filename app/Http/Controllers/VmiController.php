<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VmiController extends Controller
{
    public function index()
    {
        return view('vmi');
    }
}
