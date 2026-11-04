# Proma

A simple project manager for tracking clients, projects, and issues, built with Laravel, Livewire, and hand-written CSS.

The rebuild is in progress. See [docs/spec.md](docs/spec.md) for the specification and build phases. The previous application is on the `archive-master-2024` branch.

## Requirements

- PHP 8.4 or later
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
