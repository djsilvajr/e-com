<?php

namespace App\Http\Controllers\Web\Storefront;

use App\Http\Controllers\Controller;

class StorefrontHomeController extends Controller
{
    public function index()
    {
        return view('storefront.home');
    }
}
