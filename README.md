# Billify

A simple bill manager for personal use, built with Laravel, Livewire, and hand-written CSS. See [docs/spec.md](docs/spec.md) for the full specification.

## Local development

Requires Docker. Sail runs PHP 8.4, MySQL, and Mailpit.

```bash
composer install
cp .env.example .env
./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

- App: http://localhost:8002
- Mailpit: http://localhost:8026
- Style guide of shared components: http://localhost:8002/styleguide

## Tests

```bash
./vendor/bin/sail artisan test
```
