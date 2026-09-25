<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AskJevCommandTest extends TestCase
{
    public function test_jev_ask_fails_when_the_openrouter_key_is_missing(): void
    {
        config()->set('ai.providers.openrouter.key', null);

        Http::fake();

        $this->artisan('jev:ask', ['text' => 'Is the deploy finished?'])
            ->expectsOutputToContain('OPENROUTER_API_KEY is not set. Add it to your .env file before calling Jev.')
            ->assertFailed();

        Http::assertNothingSent();
    }

    public function test_classification_uses_the_openrouter_provider(): void
    {
        $this->assertSame('openrouter', config('ai.default_for_classification'));
        $this->assertSame('openrouter', config('ai.providers.openrouter.driver'));
        $this->assertSame('', config('ai.providers.openrouter.key'));
    }
}
