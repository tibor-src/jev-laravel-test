# Jev local test app

A local Laravel app for calling [Jev](https://docs.typesafe.ai/introduction), TypeSafe's System One decision model, through the first-party [Laravel AI SDK](https://github.com/laravel/ai) and that package's OpenRouter provider.

Jev answers typed questions about a piece of text and returns probabilities. This app asks one yes/no question: whether the text you pass is a question.

## Requirements

- PHP 8.3 or newer, with the `curl`, `mbstring`, `xml`, `zip`, and `sqlite3` extensions
- Composer 2

## Run it locally

```bash
composer install
cp .env.example .env
php artisan key:generate
```

`OPENROUTER_API_KEY` is documented in `.env.example` and is left empty. Put your OpenRouter key in `.env` when you have one. Do not commit that file.

Without a key:

```bash
php artisan jev:ask "Is the deploy finished?"
```

The command exits with a failure and prints `OPENROUTER_API_KEY is not set. Add it to your .env file before calling Jev.` It does not call OpenRouter.

With a key in `.env`, the same command sends the text through `laravel/ai`'s `openrouter` provider (`config/ai.php`, `default_for_classification`). The provider's default classification model is `~typesafe/jev-latest`. The command prints the probability that the text is a question.

## Tests

```bash
php artisan test
```

## Packages

Versions are pinned in `composer.lock`.

- `laravel/framework` — application
- `laravel/ai` — Jev classification and the OpenRouter provider
- `laravel/boost` — installed as a dev dependency (`php artisan boost:install` has already been run)
