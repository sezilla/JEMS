<?php

namespace App\Services\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait TrelloRateLimiter
{
    private const RATE_LIMIT = 100; // Trello's rate limit per 10 seconds
    private const RATE_WINDOW = 10; // 10 seconds window
    private const RETRY_AFTER = 1; // Wait 1 second between retries

    protected function checkRateLimit(): bool
    {
        $key = 'trello_api_requests_' . date('YmdHis');
        $requests = Cache::get($key, 0);

        if ($requests >= self::RATE_LIMIT) {
            Log::warning('Trello API rate limit reached', [
                'requests' => $requests,
                'limit' => self::RATE_LIMIT,
                'window' => self::RATE_WINDOW
            ]);
            return false;
        }

        Cache::put($key, $requests + 1, self::RATE_WINDOW);
        return true;
    }

    protected function waitForRateLimit(): void
    {
        $key = 'trello_api_requests_' . date('YmdHis');
        $requests = Cache::get($key, 0);

        if ($requests >= self::RATE_LIMIT) {
            Log::info('Waiting for Trello API rate limit reset', [
                'requests' => $requests,
                'limit' => self::RATE_LIMIT,
                'wait_time' => self::RETRY_AFTER
            ]);
            sleep(self::RETRY_AFTER);
        }
    }

    protected function makeRateLimitedRequest(callable $requestCallback)
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            if ($this->checkRateLimit()) {
                try {
                    return $requestCallback();
                } catch (\Exception $e) {
                    Log::error('Trello API request failed', [
                        'error' => $e->getMessage(),
                        'attempt' => $retryCount + 1
                    ]);
                    $retryCount++;
                    if ($retryCount === $maxRetries) {
                        throw $e;
                    }
                }
            } else {
                $this->waitForRateLimit();
            }
        }

        throw new \Exception('Failed to make Trello API request after ' . $maxRetries . ' attempts');
    }
}
