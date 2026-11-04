# Proma

Read `docs/spec.md` before making changes. It defines the architecture, conventions, decisions, and build phases.

## Rules

- No Tailwind, DaisyUI, Bootstrap, or any package that ships UI or CSS. All markup, CSS, tables, dialogs, and charts are hand-written.
- Alpine comes from Livewire's bundle. Never install it separately.
- Authorize every Livewire action on the server. Public properties are client-controlled input, so record identifiers use `#[Locked]`.
- Never use `{!! !!}` in views or `$guarded = []` in models.
- Keep queries portable across SQLite and MySQL.
- File uploads are out of scope.

## Commands

- `composer run test` runs Pest.
- `composer run lint` runs Pint in test mode and Larastan.
- `composer run format` applies Pint.
- `composer run dev` starts the development processes.
