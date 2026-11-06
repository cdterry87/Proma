# Proma

A simple project manager for tracking clients, projects, and issues, built with Laravel, Livewire, and hand-written CSS.

The rebuild is in progress. See [docs/spec.md](docs/spec.md) for the specification and build phases. The previous application is on the `archive-master-2024` branch.

## Requirements

- Docker
- PHP 8.4.1 or later and Composer, for the first install only

Everything else runs in containers through [Laravel Sail](https://laravel.com/docs/sail), including MySQL 8.4, Node, and the queue worker.

## Setup

```bash
composer run setup
```

```bash
./vendor/bin/sail up -d
```

```bash
./vendor/bin/sail artisan migrate
```

```bash
./vendor/bin/sail npm ci
```

```bash
./vendor/bin/sail npm run build
```

The app runs at <http://localhost:8001>. The host ports are set by `APP_PORT`, `VITE_PORT`, and `FORWARD_DB_PORT` in `.env`, and MySQL is reachable from the host on port 3307.

`node_modules` holds binaries for one platform at a time. After `sail npm ci`, run Node commands through Sail.

## Development

```bash
./vendor/bin/sail composer run dev
```

This starts Vite with hot reloading, the queue worker, and a log viewer. Stop the containers with `./vendor/bin/sail stop`.

## Users

Registration is closed. Create a user with a prompted password:

```bash
./vendor/bin/sail artisan proma:user:create
```

## Email

Password reset emails are queued, so a queue worker must be running. `sail composer run dev` starts one. Locally, `MAIL_MAILER=log` writes messages to `storage/logs/laravel.log`.

## Style reference

With the app running locally, open `/styleguide` to see every design system component. The page returns 404 outside the local and testing environments.

## Quality checks

Tests run on in-memory SQLite by default:

```bash
./vendor/bin/sail composer run test
```

Run them on MySQL, as CI also does:

```bash
./vendor/bin/sail exec -e DB_CONNECTION=mysql -e DB_DATABASE=testing laravel.test php artisan test
```

```bash
./vendor/bin/sail composer run lint
```

```bash
./vendor/bin/sail composer run format
```
