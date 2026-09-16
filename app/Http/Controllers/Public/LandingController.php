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
        $googleReviews = app(\App\Services\Reviews\GoogleReviewsService::class)->get();

        return view('public.landing', compact('instagramProfileUrl', 'instagramEmbedHtml', 'cancellationPolicyHtml', 'googleReviews'));
    }
}
