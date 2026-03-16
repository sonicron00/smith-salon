<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class AboutController extends Controller
{
    public function index()
    {
        $address = config('salon.address');
        $openingHours = config('salon.opening_hours');

        return view('public.about', compact('address', 'openingHours'));
    }
}
