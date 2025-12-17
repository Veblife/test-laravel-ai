<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\EnrichTicketWithAi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

final class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ticket_and_dispatch_enrichment_job(): void
    {
        Bus::fake();

        $payload = [
            'title' => 'Cannot login to my account',
            'description' => 'I am receiving an error when trying to login.',
        ];

        $response = $this->postJson('/api/tickets', $payload);

        $response->assertCreated()
            ->assertJson(fn ($json) => $json
                ->has('data', fn ($data) => $data
                    ->where('title', $payload['title'])
                    ->where('description', $payload['description'])
                    ->where('status', 'open')
                    ->hasAll([
                        'id', 'category', 'sentiment', 'suggested_reply', 'created_at', 'updated_at',
                    ])
                )
                ->where('enrichment', 'queued')
            );

        // Assert the ticket was persisted
        $this->assertDatabaseHas('tickets', [
            'title' => $payload['title'],
            'description' => $payload['description'],
            'status' => 'open',
        ]);

        $ticketId = (int) $response->json('data.id');

        // Assert the enrichment job was dispatched with the correct ticket id
        Bus::assertDispatched(EnrichTicketWithAi::class, function (EnrichTicketWithAi $job) use ($ticketId): bool {
            return $job->ticketId === $ticketId;
        });
    }
}
