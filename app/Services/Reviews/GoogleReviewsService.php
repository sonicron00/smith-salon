<?php

namespace App\Services\Reviews;

use App\Models\Review;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleReviewsService
{
    /**
     * Fetch reviews from the Google Places API and upsert them into the reviews table.
     * Returns the number of reviews processed, or null on failure.
     */
    public function refresh(): ?int
    {
        $apiKey = config('salon.google_api_key');
        $placeId = config('salon.google_place_id');

        if (! $apiKey || ! $placeId) {
            Log::warning('GoogleReviewsService: Missing GOOGLE_API_KEY or GOOGLE_PLACE_ID.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,reviews,googleMapsUri',
            ])->get("https://places.googleapis.com/v1/places/{$placeId}");

            if ($response->failed()) {
                Log::error('GoogleReviewsService: API request failed. ' . $response->body());
                return null;
            }

            $json = $response->json();
            $count = 0;

            foreach ($json['reviews'] ?? [] as $review) {
                $text = $review['originalText']['text'] ?? ($review['text']['text'] ?? '');

                if (empty($text)) {
                    continue;
                }

                // Google returns a stable "name" like "places/XXX/reviews/YYY" - use it for dedup
                $externalId = $review['name'] ?? md5(
                    ($review['authorAttribution']['displayName'] ?? '') . $text
                );

                Review::updateOrCreate(
                    [
                        'source' => 'google',
                        'external_id' => $externalId,
                    ],
                    [
                        'author' => $review['authorAttribution']['displayName'] ?? 'Anonymous',
                        'author_photo' => $review['authorAttribution']['photoUri'] ?? null,
                        'rating' => (int) ($review['rating'] ?? 0),
                        'text' => $text,
                        'relative_time' => $review['relativePublishTimeDescription'] ?? '',
                        'reviewed_at' => isset($review['publishTime']) ? \Carbon\Carbon::parse($review['publishTime']) : null,
                    ]
                );

                $count++;
            }

            return $count;
        } catch (\Throwable $e) {
            Log::error('GoogleReviewsService: ' . $e->getMessage());
            report($e);
            return null;
        }
    }

    /**
     * Get the reviews to display on the homepage, along with summary stats.
     */
    public function get(): array
    {
        $reviews = Review::query()
            ->visible()
            ->where('source', 'google')
            ->orderByDesc('reviewed_at')
            ->get();

        $placeId = config('salon.google_place_id');

        return [
            'rating' => $reviews->avg('rating'),
            'total' => $reviews->count(),
            'reviews' => $reviews->map(fn (Review $r) => [
                'author' => $r->author,
                'author_photo' => $r->author_photo,
                'rating' => $r->rating,
                'text' => $r->text,
                'relative_time' => $r->relative_time,
            ])->toArray(),
            'maps_uri' => "https://search.google.com/local/reviews?placeid={$placeId}",
        ];
    }
}
