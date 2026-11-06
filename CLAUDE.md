# Proma

Read `docs/spec.md` before making changes. It defines the architecture, conventions, decisions, and build phases.

## Rules

- No Tailwind, DaisyUI, Bootstrap, or any package that ships UI or CSS. All markup, CSS, tables, dialogs, and charts are hand-written.
- Alpine comes from Livewire's bundle. Never install it separately.
- Authorize every Livewire action on the server. Public properties are client-controlled input, so record identifiers use `#[Locked]`.
- Never use `{!! !!}` in views or `$guarded = []` in models.
- Keep queries portable across SQLite and MySQL.
- File uploads are out of scope.
- The Content Security Policy forbids inline styles and `eval`. Register Alpine logic with `Alpine.data()`, and never write `style` attributes in server-rendered HTML.
- Hard-coded colors belong only in `resources/css/base/tokens.css`. Add new tokens to the contrast test.

## Commands

Local development runs on Laravel Sail with MySQL. The app is at `http://localhost:8001`. Prefix commands with `./vendor/bin/sail`, and always run Node commands through it, because `node_modules` holds Linux binaries.

- `sail composer run test` runs Pest on in-memory SQLite.
- `sail exec -e DB_CONNECTION=mysql -e DB_DATABASE=testing laravel.test php artisan test` runs Pest on MySQL.
- `sail composer run lint` runs Pint in test mode and Larastan.
- `sail composer run format` applies Pint.
- `sail composer run dev` starts Vite, the queue worker, and the log viewer.
