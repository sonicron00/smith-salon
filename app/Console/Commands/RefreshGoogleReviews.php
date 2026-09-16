<?php

namespace App\Console\Commands;

use App\Services\Reviews\GoogleReviewsService;
use Illuminate\Console\Command;

class RefreshGoogleReviews extends Command
{
    protected $signature = 'reviews:refresh';

    protected $description = 'Fetch the latest Google reviews and cache them';

    public function handle(GoogleReviewsService $service): int
    {
        $count = $service->refresh();

        if ($count === null) {
            $this->error('Failed to refresh Google reviews. Check logs.');
            return self::FAILURE;
        }

        $this->info("Upserted {$count} review(s).");
        return self::SUCCESS;
    }
}
