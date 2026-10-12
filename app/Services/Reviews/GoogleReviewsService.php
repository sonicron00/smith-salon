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

        // The Places API only returns 5 reviews per call, and defaults to "most relevant"
        // which tends to be the same 5 each time. Fetching both sort orders lets us
        // accumulate a wider set over repeated runs via the upsert.
        $seenIds = [];
        $total = 0;

        foreach (['RELEVANCE', 'NEWEST'] as $sort) {
            $result = $this->fetchAndStore($apiKey, $placeId, $sort, $seenIds);

            if ($result === null) {
                // If the first call fails outright, bail; otherwise keep what we got
                if ($sort === 'RELEVANCE') {
                    return null;
                }
                continue;
            }

            $total += $result;
        }

        return $total;
    }

    /**
     * Fetch one page of reviews with the given sort order and upsert them.
     * Tracks already-seen external IDs to avoid double counting across sort orders.
     */
    private function fetchAndStore(string $apiKey, string $placeId, string $sort, array &$seenIds): ?int
    {
        try {
            $response = Http::withHeaders([
                'X-Goog-Api-Key' => $apiKey,
                'X-Goog-FieldMask' => 'displayName,rating,userRatingCount,reviews,googleMapsUri',
            ])->get("https://places.googleapis.com/v1/places/{$placeId}", [
                'reviews_sort' => $sort,
            ]);

            if ($response->failed()) {
                Log::error("GoogleReviewsService: API request failed (sort={$sort}). " . $response->body());
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

                if (in_array($externalId, $seenIds, true)) {
                    continue; // already processed in another sort order this run
                }
                $seenIds[] = $externalId;

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
            Log::error("GoogleReviewsService (sort={$sort}): " . $e->getMessage());
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
