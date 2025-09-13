<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BauCuaController extends Controller
{
    public function index()
    {
        return view('baucua.index');
    }
}
