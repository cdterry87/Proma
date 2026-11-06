# Proma

A simple project manager for tracking clients, projects, and issues, built with Laravel, Livewire, and hand-written CSS.

The rebuild is in progress. See [docs/spec.md](docs/spec.md) for the specification and build phases. The previous application is on the `archive-master-2024` branch.

## Requirements

- PHP 8.4.1 or later
- Composer
- Node 20.19 or later

## Setup

```bash
composer run setup
```

This installs dependencies, creates `.env`, migrates a local SQLite database, and builds assets.

## Development

```bash
composer run dev
```

## Users

Registration is closed. Create a user with a prompted password:

```bash
php artisan proma:user:create
```

## Email

Password reset emails are queued, so a queue worker must be running. `composer run dev` starts one. Locally, `MAIL_MAILER=log` writes messages to `storage/logs/laravel.log`.

## Style reference

With the app running locally, open `/styleguide` to see every design system component. The page returns 404 outside the local and testing environments.

## Quality checks

```bash
composer run test
```

```bash
composer run lint
```

```bash
composer run format
```
