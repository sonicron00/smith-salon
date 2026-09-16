<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;

class TreatmentsController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->where('active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $imageOne = \App\Models\Setting::get('treatments.image_one');
        $imageTwo = \App\Models\Setting::get('treatments.image_two');

        return view('public.treatments', compact('services', 'imageOne', 'imageTwo'));
    }
}
