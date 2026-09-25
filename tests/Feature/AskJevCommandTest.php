<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Laravel\Ai\Classification;
use Laravel\Ai\Responses\Data\BooleanAnswer;
use Tests\TestCase;

class AskJevCommandTest extends TestCase
{
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

    public function test_jev_ask_reports_the_probability_from_the_classifier(): void
    {
        // A configured key is required to reach the classifier; the classification
        // itself is faked so the test never performs a real OpenRouter request and
        // stays deterministic regardless of whether OPENROUTER_API_KEY is set.
        config()->set('ai.providers.openrouter.key', 'test-key');

        Classification::fake([
            ['is_question' => new BooleanAnswer(0.97)],
        ]);

        $this->artisan('jev:ask', ['text' => 'Is the deploy finished?'])
            ->expectsOutputToContain('Probability this is a question: 0.97')
            ->assertSuccessful();

        Classification::assertClassified(
            fn ($prompt) => $prompt->contains('Is the deploy finished?') && $prompt->asks('is_question')
        );
    }
}
