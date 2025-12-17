<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Ai\AiProvider;
use App\Services\Ai\Providers\MockAiProvider;
use App\Services\Ai\Providers\OpenAiChatGpt;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, fn () => new Client());

        $this->app->bind(AiProvider::class, function () {
            $testMode = (bool) config('services.ai.test_mode', false);

            if ($testMode) {
                return $this->app->make(MockAiProvider::class);
            }

            $provider = (string) config('services.ai.provider', 'openai');

            return match ($provider) {
                'openai', 'chatgpt' => $this->app->make(OpenAiChatGpt::class),
                'mock' => $this->app->make(MockAiProvider::class),
                default => throw new \RuntimeException("Unknown AI provider: {$provider}"),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
