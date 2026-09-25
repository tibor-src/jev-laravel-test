<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AskJevCommandTest extends TestCase
{
    /**
     * Whether a usable OpenRouter API key is configured for this run.
     *
     * The suite adapts to this value: key-less assertions run only when no key
     * is present, and the live classification test runs only when one is. This
     * keeps `php artisan test` green whether or not OPENROUTER_API_KEY is set.
     */
    private function hasOpenRouterKey(): bool
    {
        return filled(config('ai.providers.openrouter.key'));
    }

    public function test_classification_defaults_to_the_openrouter_provider(): void
    {
        $this->assertSame('openrouter', config('ai.default_for_classification'));
        $this->assertSame('openrouter', config('ai.providers.openrouter.driver'));
    }

    public function test_jev_ask_fails_when_the_openrouter_key_is_missing(): void
    {
        config()->set('ai.providers.openrouter.key', null);

        Http::fake();

        $this->artisan('jev:ask', ['text' => 'Is the deploy finished?'])
            ->expectsOutputToContain('OPENROUTER_API_KEY is not set. Add it to your .env file before calling Jev.')
            ->assertFailed();

        Http::assertNothingSent();
    }

    public function test_no_openrouter_key_is_configured_by_default(): void
    {
        if ($this->hasOpenRouterKey()) {
            $this->markTestSkipped('OPENROUTER_API_KEY is set; the key-less default does not apply.');
        }

        $this->assertSame('', config('ai.providers.openrouter.key'));
    }

    public function test_jev_ask_returns_a_probability_with_a_real_key(): void
    {
        if (! $this->hasOpenRouterKey()) {
            $this->markTestSkipped('OPENROUTER_API_KEY is not set; skipping the live OpenRouter classification.');
        }

        $this->artisan('jev:ask', ['text' => 'Is the deploy finished?'])
            ->expectsOutputToContain('Probability this is a question:')
            ->assertSuccessful();
    }
}
