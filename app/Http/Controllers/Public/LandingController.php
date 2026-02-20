<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class LandingController extends Controller
{
    public function index()
    {
        $instagramProfileUrl = Setting::get('instagram.profile_url');
        $instagramEmbedHtml  = Setting::get('instagram.embed_html');

        $cancellationPolicyHtml = Setting::get('policy.cancellation_html');

        return view('public.landing', compact('instagramProfileUrl', 'instagramEmbedHtml', 'cancellationPolicyHtml'));
    }
}
