<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Mostrar landing page público
     */
    public function index()
    {
        return view('public.landing');
    }
}
