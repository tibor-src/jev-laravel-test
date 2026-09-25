<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Boolean;
use Laravel\Ai\Responses\Data\BooleanAnswer;

class AskJevCommand extends Command
{
    protected $signature = 'jev:ask {text : Text for Jev to judge}';

    protected $description = 'Ask Jev, through the OpenRouter provider, whether the text is a question';

    public function handle(): int
    {
        $key = config('ai.providers.openrouter.key');

        if (! is_string($key) || blank($key)) {
            $this->error('OPENROUTER_API_KEY is not set. Add it to your .env file before calling Jev.');

            return self::FAILURE;
        }

        $text = $this->argument('text');

        if (! is_string($text) || blank($text)) {
            $this->error('Provide some text for Jev to judge.');

            return self::INVALID;
        }

        $response = Classification::of($text)
            ->question('is_question', new Boolean('Is this a question?'))
            ->classify('openrouter');

        $answer = $response->answer('is_question');

        if (! $answer instanceof BooleanAnswer) {
            $this->error('Jev did not return a yes/no probability.');

            return self::FAILURE;
        }

        $this->info('Probability this is a question: '.$answer->probability);

        return self::SUCCESS;
    }
}
