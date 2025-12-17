<?php

declare(strict_types=1);

namespace App\Services\Ai\Providers;

use App\Enums\TicketCategory;
use App\Enums\TicketSentiment;
use App\Services\Ai\AiProvider;
use App\Services\Ai\Dto\AiEnrichmentResult;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

final class OpenAiChatGpt implements AiProvider
{
    public function __construct(
        private readonly ClientInterface $http,
    ) {
    }

    /**
     * Expects model to return JSON:
     * { "category": "...", "sentiment": "...", "reply": "..." }
     *
     * @throws GuzzleException
     */
    public function enrichTicket(string $description): AiEnrichmentResult
    {
        $apiKey = (string) config('services.ai.openai.api_key');
        $model = (string) config('services.ai.openai.model', 'gpt-4o-mini');
        $baseUrl = (string) config('services.ai.openai.base_url', 'https://api.openai.com/v1');
        $systemPrompt = (string) config('services.ai.system_prompt', '');

        if ($apiKey === '') {
            throw new \RuntimeException('OpenAI API key is not configured (services.ai.openai.api_key).');
        }

        $system = trim($systemPrompt) !== '' ? $systemPrompt : $this->defaultSystemPrompt();

        $response = $this->http->request('POST', rtrim($baseUrl, '/').'/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer '.$apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'temperature' => 0.2,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $description],
                ],
            ],
            'timeout' => 30,
        ]);

        $payload = json_decode((string) $response->getBody(), true);

        if (! is_array($payload)) {
            throw new \RuntimeException('OpenAI response is not valid JSON');
        }

        $content = Arr::get($payload, 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            Log::warning('OpenAI returned empty content', ['payload' => $payload]);
            return $this->fallbackResult($payload);
        }

        $resultPayload = json_decode($content, true);

        if (! is_array($resultPayload)) {
            Log::warning('OpenAI content is not JSON, falling back', ['content' => $content]);
            return $this->fallbackResult($payload, $content);
        }

        $categoryRaw = Arr::get($resultPayload, 'category');
        $sentimentRaw = Arr::get($resultPayload, 'sentiment');
        $replyRaw = Arr::get($resultPayload, 'reply');

        $category = TicketCategory::tryFrom(is_string($categoryRaw) ? $categoryRaw : '') ?? TicketCategory::General;
        $sentiment = TicketSentiment::tryFrom(is_string($sentimentRaw) ? $sentimentRaw : '') ?? TicketSentiment::Neutral;
        $reply = is_string($replyRaw) ? $replyRaw : null;

        return new AiEnrichmentResult(
            category: $category,
            sentiment: $sentiment,
            reply: $reply,
            raw: $payload,
        );
    }

    private function fallbackResult(array $raw, ?string $content = null): AiEnrichmentResult
    {
        return new AiEnrichmentResult(
            category: TicketCategory::General,
            sentiment: TicketSentiment::Neutral,
            reply: $content,
            raw: $raw,
        );
    }

    private function defaultSystemPrompt(): string
    {
        return implode("\n", [
            'You are an assistant that classifies support tickets.',
            'Return ONLY valid minified JSON with keys: category, sentiment, reply.',
            'category must be one of: General, Billing, Technical, Account, Refund, Shipping.',
            'sentiment must be one of: Positive, Neutral, Negative.',
            'reply must be a helpful short reply in the same language as the user.',
        ]);
    }
}
