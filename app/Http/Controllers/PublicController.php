<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function index()
    {
        return view('public.login');
    }

    public function registro()
    {
        return view('public.registrar');
    }



}
