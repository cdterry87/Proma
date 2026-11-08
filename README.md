# Proma

A simple project manager for tracking clients, projects, and issues, built with Laravel, Livewire, and hand-written CSS.

See [docs/spec.md](docs/spec.md) for the specification, decisions, and acceptance checklist. The previous application is on the `archive-master-2024` branch.

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
./vendor/bin/sail artisan user:create
```

## Email

Password reset emails are queued, so a queue worker must be running. `sail composer run dev` starts one. Locally, `MAIL_MAILER=log` writes messages to `storage/logs/laravel.log`.

## Demo mode

Demo mode creates an account whose data is rebuilt every night, and adds a "Try the demo" button to the login page that signs visitors in to it without showing the credentials. Set these in `.env`:

```dotenv
PROMA_DEMO_ENABLED=true
PROMA_DEMO_EMAIL=demo@example.com
PROMA_DEMO_PASSWORD=choose-a-password
```

Demo accounts are capped at a fixed number of each record, set under `demo.limits` in `config/proma.php`, so visitors cannot fill the database before the nightly reset.

Cloudflare Turnstile guards the demo button when both keys are set. `.env.example` has Cloudflare's test keys, which always pass. In production, create a widget in the Cloudflare dashboard for your domain and use its keys:

```dotenv
TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key
```

Then create the account:

```bash
./vendor/bin/sail artisan db:seed --class=DemoUserSeeder
```

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

## Deployment

The project has no deployment pipeline yet. A server needs:

- PHP 8.4.1 or later, and MySQL 8.4 or SQLite
- `composer install --no-dev --optimize-autoloader`, then `npm ci` and `npm run build`
- `php artisan migrate --force` and `php artisan optimize`
- A cron entry that runs `php artisan schedule:run` every minute
- A supervised `php artisan queue:work`, which sends all email
- Working SMTP settings, with `APP_ENV=production` and `APP_DEBUG=false`
- `APP_TIMEZONE` set to the local timezone, because the nightly jobs and date comparisons use it
- HTTPS, with `APP_URL` set to the public `https://` address. In production the session cookie is sent only over HTTPS, and requests for any other host get a 400.
- Behind a load balancer or proxy, trusted proxies configured with `$middleware->trustProxies()` in `bootstrap/app.php`. Otherwise every visitor shares the proxy's IP address, and with it the sign in and password reset rate limits.

On a production server, `db:seed` also needs `--force`.
