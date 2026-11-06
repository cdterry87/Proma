# Proma Specification

Proma is a simple project manager for tracking clients, projects, and issues. This document records what the current application does and defines how it will be rebuilt from scratch on the latest Laravel, Livewire, and Alpine, with completely custom CSS.

| | |
|---|---|
| **Status** | Draft with decisions recorded |
| **Analyzed** | `master` at `3a9412b` (last commit 2024-08-10) |
| **Last updated** | 2026-11-04 |

**How to read this document**

- [Part 1](#part-1-current-application) describes the application as it exists today. Use it as the reference for feature parity, apart from features Part 5 removes, such as file uploads.
- [Part 2](#part-2-known-defects) lists defects and gaps that must not be carried forward.
- [Part 3](#part-3-rebuild-specification) specifies the new application.
- [Part 4](#part-4-migration-plan) covers the branch reset and build phases.
- [Part 5](#part-5-decisions) records the decisions that shape the rebuild.
- [Appendix A](#appendix-a-feature-parity-checklist) is the acceptance checklist.

**Basis of the analysis.** Everything in Parts 1 and 2 comes from reading the source. The application was not run, because `vendor/` and `node_modules/` are not installed and no local MySQL is available. Findings that depend on runtime behavior of a third-party package are marked *unverified*.

---

## Part 1: Current application

### 1.1 Purpose and users

Proma is a single-user-per-account workspace. Each user sees only the clients, projects, and issues they own; there is no sharing, team, or role model. The hierarchy is:

```
User
└── Client ── Contacts, Uploads
    ├── Project ── Tasks, Uploads
    │   └── Issue (optional link)
    └── Issue ── Tasks, Uploads
```

There are two kinds of user:

- **Regular users** have full access. Accounts are created only by seeders; there is no registration page.
- **Demo users** (`users.demo = true`) can do everything except upload files. Their data is wiped and regenerated every night. The login page publishes the demo credentials.

### 1.2 Technology stack

| Concern | Current choice |
|---|---|
| Framework | Laravel 10, PHP `^8.1` |
| UI runtime | Livewire 3.4 with its bundled Alpine |
| Styling | Tailwind CSS 3.4 and DaisyUI 4 |
| Data tables | `power-components/livewire-powergrid` 5.4, with `openspout` for export |
| Charts | `asantibanez/livewire-charts` 3 |
| Auth guard | `auth:sanctum` and `verified` middleware on web routes |
| Database | MySQL 8 |
| Local environment | Laravel Sail (MySQL, Redis, Meilisearch, Mailpit, Selenium) |
| Tests | PHPUnit 10 |
| Queue, cache, session | `sync`, `file`, `file` |
| Mail | SendGrid SMTP configured, but nothing sends mail |

Declared but unused: `flatpickr`, Sanctum API tokens (only the default `/api/user` route), Redis, Meilisearch, broadcasting, and the `password_reset_tokens` table. `axios` is loaded in `bootstrap.js` but no application code calls it.

### 1.3 Application structure

The application has no controllers. Every page is a full-page Livewire component, and business logic lives directly in those components.

| Path | Contents |
|---|---|
| `app/Livewire/` | `Home`, `Login`, `Notifications`, `NotificationsTable`, `PrivacyPolicy`, `Settings`, `SideBarUserDetails` |
| `app/Livewire/{Clients,Projects,Issues}/` | For each entity: page, view page, form, table, plus sub-resource forms and tables |
| `app/Models/` | 14 models |
| `app/Jobs/` | Three notification jobs and `RoutineCleanupJob` |
| `app/Traits/` | `WithModal` (resets a form when a modal closes), `WithActiveToggle` (Yes/No codes) |
| `resources/views/components/` | `alerts`, `elements`, `icons` (45 SVGs), `inputs`, `layouts`, `modals` |
| `resources/views/errors/` | 403, 404, 503 |

Components communicate through Livewire events:

| Event | Purpose |
|---|---|
| `refreshData` | Reloads tables and stat counters after a write |
| `refreshUser` | Updates the sidebar after a settings change |
| `closeModal` | Resets the form state of every `WithModal` component on the page |
| `edit`, `editTask`, `editContact` | Loads a record into a modal form |
| `getClient`, `getProject`, `getIssue` | Passes the parent record to a sub-resource form |
| `deleteTask`, `deleteContact`, `deleteFile`, `downloadFile` | Row actions from tables |

### 1.4 Data model

All tables have `id` and timestamps unless noted. All foreign keys cascade on delete and update.

**`users`**

| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `email` | string | Unique |
| `title` | string, nullable | Defaults to `User` |
| `email_verified_at` | timestamp, nullable | Not enforced |
| `password` | string | `hashed` cast |
| `demo` | boolean | **Defaults to `true`** (see D-13) |
| `remember_token` | string | |

**`clients`**: `user_id`, `name` (indexed), `description` (nullable text), `active` (boolean, default true).

**`clients_contacts`**: `client_id`, `name` (indexed), `title`, `email`, `phone` (30, nullable), `phone_ext` (5, nullable), `active` (boolean, default true).

**`projects`**: `user_id`, `client_id`, `name` (indexed), `description` (nullable text), `start_date`, `due_date`, `completed_date` (all nullable dates). A project is complete when `completed_date` is set.

**`projects_tasks`**: `project_id`, `title` (indexed), `description` (nullable text), `start_date`, `due_date`, `completed_date` (all nullable dates).

**`issues`**: `user_id`, `name` (indexed), `description` (text), `priority` (smallint), `resolved_date` (nullable date), `client_id`, `project_id` (nullable). An issue is resolved when `resolved_date` is set. Priority values:

| Value | Label |
|---|---|
| 1 | Low |
| 2 | Medium |
| 3 | High |
| 4 | Critical |

**`issues_tasks`**: `issue_id`, `title` (indexed), `description` (nullable text), `completed_date` (nullable date). Unlike project tasks, issue tasks have no start or due date.

**`clients_uploads`, `projects_uploads`, `issues_uploads`**: parent foreign key, `name`, `path`, `type` (nullable), `size` (nullable string, in bytes).

**`users_notifications`**: `user_id`, `subject`, `message` (text), `read` (boolean, default false).

**Notification dedupe tables**, which record that a notification was already generated:

| Table | Columns |
|---|---|
| `projects_notifications_sent` | `user_id`, `project_id`, `due`, `overdue`, `completed` |
| `projects_tasks_notifications_sent` | `user_id`, `project_task_id`, `due`, `overdue`, `completed` |
| `issues_notifications_sent` | `user_id`, `issue_id`, `priority`, `resolved` |

**Framework tables**: `sessions` (migrated, although `SESSION_DRIVER=file`), `password_reset_tokens`, `failed_jobs`, `personal_access_tokens`.

### 1.5 Routes

| Method | URL | Name | Component | Access |
|---|---|---|---|---|
| GET | `/login` | `login` | `Login` | Public |
| POST | `/logout` | `logout` | Closure | Public |
| GET | `/privacy-policy` | `privacy-policy` | `PrivacyPolicy` | Public; layout depends on auth state |
| GET | `/` | `home` | `Home` | Auth |
| GET | `/projects` | `projects` | `Projects\Projects` | Auth |
| GET | `/projects/{project}` | `projects.view` | `Projects\ProjectsView` | Auth, owner |
| GET | `/issues` | `issues` | `Issues\Issues` | Auth |
| GET | `/issues/{issue}` | `issues.view` | `Issues\IssuesView` | Auth, owner |
| GET | `/clients` | `clients` | `Clients\Clients` | Auth |
| GET | `/clients/{client}` | `clients.view` | `Clients\ClientsView` | Auth, owner |
| GET | `/notifications` | `notifications` | `Notifications` | Auth |
| GET | `/settings` | `settings` | `Settings` | Auth |
| GET | `/api/user` | | Closure | Sanctum; unused |

### 1.6 Features

#### Authentication

- Email and password login, with a "Remember Me" checkbox, redirecting to the intended URL or home.
- A failed login shows "Email or password is incorrect."
- Logout invalidates the session and redirects to login with a success message.
- The login page has a background video with a dark overlay, the logo and tagline "Simplify your project management.", a box with the demo credentials and a daily-reset note, the copyright, a privacy link, and a "Developed by" credit.
- There is no registration, password reset, or email verification.

#### Dashboard (`/`)

A timeframe selector offers the last 30, 90, 180, or 365 days (default 30) and updates the page live.

| Section | Content |
|---|---|
| Stat tiles | Projects Created, Projects Completed, Issues Opened, Issues Resolved |
| Change indicator | Each tile compares the period with the preceding period of equal length, shown as `+N% Increase`, `-N% Decrease`, or `0% Change`. It shows 0 when the prior period is empty. |
| Incomplete Projects | Five incomplete projects by `due_date` descending, with name (truncated to 25 characters, linked) and due date |
| Unresolved Issues | Five latest unresolved issues, with name (linked) and open date |
| Projects vs Issues | Multi-line chart of records created, grouped by month, within the timeframe |
| Issue Priorities | Pie chart of issues created within the timeframe by priority |

Each section has an empty state ("No incomplete projects found.", "Not enough data.", and so on).

#### Clients

**List (`/clients`)** shows Name, Projects (incomplete count), Issues (unresolved count), Contacts count, Uploads count, Active, Updated, and a View action. It offers search on name, sorting, an Active filter, an expandable row showing the description, export, per-page selection, and a record count. "Add Client" opens the form in a modal.

**Detail (`/clients/{client}`)** shows:

- An active or inactive badge, name, description, and created and updated timestamps.
- Stats: incomplete projects (with complete count) and unresolved issues (with resolved count).
- A Contacts table and an Uploads table.
- "Edit Details" (modal) and "Delete Client" (confirmation modal, cascades to all child records).

**Form validation:** `name` required, max 255; `description` required. `active` is a toggle, default on.

**Contacts** have a table of Name, Title, Email, Phone, Ext., Active, with search, sort, an Active filter, export, edit (modal), and immediate delete with no confirmation.

**Contact validation:** `name`, `title`, and `email` required, max 255, valid email; `phone` max 30; `phone_ext` max 5.

#### Projects

**List (`/projects`)** shows Name, Client, Start Date, Due Date, Completed, Last Updated, and a View action. Empty dates display as "Not Started", "Not Due", or "Incomplete". Filters are Started / Not Started, Past Due / Not Due, and Completed / Incomplete. It also has search, sort, an expandable description, export, and pagination controls.

**Detail (`/projects/{project}`)** shows:

- A completed or incomplete badge, name, description, client, start and due dates, and timestamps.
- Stats: unresolved issues (with resolved count) and incomplete tasks (with complete count).
- A Tasks table and an Uploads table.
- "Edit Details" (modal, with a Complete / Incomplete toggle) and "Delete Project" (confirmation, cascades).

**Form validation:** `name` required, max 255; `description` required; `client_id` required and existing; dates optional and valid. The client list contains only the user's active clients.

**Tasks** have a table of Title, Start Date, Due, and Completed, sorted by due date descending, with search, sort, filters (Started, Past Due, Completed), an expandable description, export, edit in a modal with a Complete / Incomplete toggle, and immediate delete.

**Task validation:** `title` required, max 255; `description` required; dates optional and valid.

#### Issues

**List (`/issues`)** shows Name, Priority, Client, Project, Resolved, Last Updated, and a View action. Filters are Priority and Resolved / Unresolved. It also has search, sort, an expandable description, export, and pagination controls.

**Detail (`/issues/{issue}`)** shows:

- A resolved or unresolved badge, a priority badge, name, description, client, project, and timestamps.
- Stats: incomplete tasks (with complete count).
- A Tasks table and an Uploads table.
- "Edit Details" (modal, with a Resolve / Unresolve toggle) and "Delete Issue" (confirmation, cascades).

**Form validation:** `name`, `description`, `priority`, and `client_id` required; `project_id` optional. The client list is limited to active clients. Selecting a client reveals a project list limited to that client's incomplete projects.

**Tasks** have Title and Completed columns, sorted by last update, with search, sort, a Completed filter, an expandable description, export, edit with a Complete / Incomplete toggle, and immediate delete. Task validation: `title` required, max 255; `description` required.

#### File uploads

Clients, projects, and issues share the same behavior:

- Multiple files per upload, each at most 10 MB.
- Allowed types: `doc, docx, pdf, ppt, pptx, rtf, txt, csv, xls, xlsx, gif, jpg, jpeg, png, svg, zip, rar, 7z`.
- Stored on the `local` disk at `{clients|projects|issues}/{parentId}/{originalFilename}`.
- A table shows Name, Type, Size, and Uploaded, with search, sort, export, download, and immediate delete.
- Demo users see "Uploads are disabled for demo users." instead of the upload button, and the server action returns early.

#### Notifications

**In-app only.** The Notifications page shows Subject, Received, and Read, with search, sort, a Read / Unread filter, export, and "Mark All As Read". Expanding a row displays the message and marks that notification as read.

Scheduled jobs generate notifications:

| Time (server) | Job | Purpose |
|---|---|---|
| 00:00 | `RoutineCleanupJob` | Reset demo data |
| 02:00 | `IssuesNotificationsJob` | Issue notifications |
| 04:00 | `ProjectsNotificationsJob` | Project notifications |
| 06:00 | `ProjectsTasksNotificationsJob` | Project task notifications |

Each rule fires once per record, tracked in the dedupe tables:

| Trigger | Subject |
|---|---|
| Issue resolved | Issue Resolved |
| Critical issue open more than 3 days | Critical Issue |
| High issue open more than 7 days | High Issue |
| Medium issue open more than 14 days | Medium Issue |
| Low issue open more than 30 days | Low Priority Issue |
| Project due in exactly 10 days | Project Due Reminder |
| Project past due and incomplete | Project Overdue |
| Project completed | Project Completed |
| Task due in exactly 10 days | Project Task Due Reminder |
| Task past due and incomplete | Project Task Overdue |
| Task completed | Project Task Completed |

Issue tasks generate no notifications.

#### Demo reset

Nightly, `RoutineCleanupJob` deletes all clients, projects, issues, notifications, and dedupe rows for demo users, then runs `DemoUserSeeder`. The seeder creates 10–20 clients, 5–15 projects per client, and 1–5 issues per project for the oldest demo user. It creates no contacts, tasks, or uploads.

#### Settings

Name, email, title, and password each have their own card with an inline edit mode and a separate save action. Saving updates the sidebar.

| Field | Validation |
|---|---|
| Name | Required, min 6, max 255 |
| Email | Required, valid, unique, max 255 |
| Title | Optional, max 255 |
| Password | Required, min 6, confirmed |

#### Application shell

- A responsive sidebar drawer, always open on large screens, with the logo, a "Welcome" card (name and title), navigation (Home, Projects, Issues, Clients, Notifications, Settings), and Log Out. The current section is highlighted.
- A header with the theme toggle and a mobile menu button.
- A footer with the copyright and privacy policy link.
- Dark and light themes, stored in `localStorage`, defaulting to dark.
- Flash alerts for error, warning, info, and success.
- On detail pages, long values collapse to 150 characters behind Show More and Show Less, and line breaks are preserved.
- Custom 403, 404, and 503 pages with illustrations. The 403 and 404 pages link home.
- A privacy policy page with nine sections, effective 01/01/2024, shown in either layout.
- Favicons and a web manifest.

**Visual identity**

| Token | Light | Dark |
|---|---|---|
| `primary` | `#494694` | `#8b86eb` |
| `secondary` | `#336a6b` | `#63c2af` |
| `accent` | `#374151` | `#e1e8e7` |
| `neutral` | `#344052` | `#111827` |
| `base-100` | `#ffffff` | `#29374a` |
| `base-200` | `#e5e7eb` | `#1f2937` |
| `base-300` | `#d5d9e0` | `#1b2431` |

Status colors are the same in both themes: info `#1d4ed8`, success `#047857`, warning `#6d28d9`, error `#be123c`. The logo uses the Kaushan Script font.

### 1.7 Tests

There are 43 feature tests across 21 files, using PHPUnit and a MySQL `testing` database. They cover page access for guests and users, form saves, edits, status toggles, upload and download flows, and each scheduled job. A coverage report is generated at deploy time. No test name indicates coverage of cross-user access to sub-resources.

### 1.8 Build and deployment

1. `deploy.sh` runs the tests through Sail, pushes, merges `master` into `production`, and pushes `production`.
2. A push to `production` triggers `.github/workflows/main.yml`, which builds assets, commits them to a force-pushed `deploy` branch, and connects over SSH.
3. `server_deploy.sh` in `/var/www/proma` enters maintenance mode, resets to `origin/deploy`, runs `composer install` (including dev dependencies), migrates, clears caches, and exits maintenance mode.

The workflow runs no tests. `production` and `deploy` were last updated on 2024-07-09, so the later commits on `master` have not been deployed through this path. The repository does not document the server's scheduler cron entry.

---

## Part 2: Known defects

These must not be carried into the rebuild. Each maps to a requirement in Part 3.

### Security

| ID | Defect | Location |
|---|---|---|
| D-1 | **Missing ownership checks on most actions.** Any logged-in user can read, edit, delete, or download another user's contacts, tasks, and uploads by supplying its ID. The same applies to the complete and resolve toggles. Only the three detail pages and the main form `edit` methods check ownership. | `*ContactsForm`, `*TasksForm`, `*UploadsForm`, `toggleComplete*`, `toggleResolveIssue` |
| D-2 | **Record takeover through tampered state.** Main forms use `updateOrCreate(['id' => $this->model_id], ['user_id' => auth()->id(), ...])`. `model_id` is an unlocked public Livewire property, so changing it overwrites another user's record and reassigns it. | `ClientsForm`, `ProjectsForm`, `IssuesForm` |
| D-3 | **Foreign keys not scoped to the owner.** `exists:clients,id` and `exists:projects,id` allow linking to another user's records. | `ProjectsForm`, `IssuesForm` |
| D-4 | **Stored XSS.** Descriptions and messages are printed unescaped with `{!! !!}`. Notification messages embed record names. | `*-details.blade.php`, `notifications-details.blade.php` |
| D-5 | **No login throttling.** | `Login` |
| D-6 | **Password change does not require the current password.** Email changes require no confirmation either. | `Settings` |
| D-7 | **SVG uploads are accepted** and files keep their client-supplied names. | `*UploadsForm` |
| D-8 | **A real account is seeded with a hardcoded weak password.** If production still uses that password, rotate it now, independently of this rebuild. | `DatabaseSeeder` |
| D-9 | **Demo users can change their own password and email,** which locks other visitors out of the demo. | `Settings` |

### Functional

| ID | Defect | Location |
|---|---|---|
| D-10 | **The Issues list breaks on an issue with no project.** `$model->project->name` reads a property of null, although the project is optional. | `IssuesTable` |
| D-11 | **Task notifications have blank names.** They read `$task->name`, but the column is `title`. | `ProjectsTasksNotificationsJob` |
| D-12 | **Reminder text is wrong.** Queries match 10 days out, and the message says "5 days". The exact-date match also skips the reminder if the scheduler misses that day. | `ProjectsNotificationsJob`, `ProjectsTasksNotificationsJob` |
| D-13 | **`users.demo` defaults to `true`.** A user created without an explicit value has all of their data wiped that night. | Users migration |
| D-14 | **The demo reset does not do what the login page says.** The page says user settings reset daily, but the profile and password are not restored. Only the oldest demo user is reseeded, although all are wiped. | `RoutineCleanupJob`, `DemoUserSeeder` |
| D-15 | **The demo reset needs a dev dependency in production.** It uses factories that require Faker, which is in `require-dev`, so a `--no-dev` deploy would break it. | `RoutineCleanupJob` |
| D-16 | **Missing dates show today's date.** `Carbon::parse(null)` returns now, so the `?? 'N/A'` fallback never runs. | `projects-view`, `home` |
| D-17 | **An issue can link to a project of a different client.** `project_id` is not cleared when the client changes and no rule enforces the match. | `IssuesForm` |
| D-18 | **Editing can lose context.** Inactive clients and completed projects are excluded from the selects, so an existing link to one has no matching option. | `ProjectsForm`, `IssuesForm` |
| D-19 | **Priority is not validated against its allowed values.** An invalid value breaks rendering. | `IssuesForm` |
| D-20 | **Date order is not validated.** A due date can precede its start date. | Project and task forms |
| D-21 | **Files are orphaned and can be overwritten.** Deleting an upload or parent leaves the file on disk. Same-named files overwrite each other while both rows remain. Each file is also written twice. | `*UploadsForm` |
| D-22 | **Custom upload error messages never apply.** The messages are keyed to `files.required`, `files.max`, and `files.mimes`, while the rules are on `files.*`. | `*UploadsForm` |
| D-23 | **Authenticated visitors can still see `/login`,** and `RouteServiceProvider::HOME` points to `/home`, which does not exist. | `routes/web.php` |
| D-24 | **Form behavior after saving is inconsistent.** Modals stay open. Saving a new contact twice updates it, while saving a new project twice creates a duplicate. Settings cards stay in edit mode. | Forms |
| D-25 | **The dashboard's chart conditions are wrong.** The line chart is hidden unless issues exist, and the pie chart shows when only projects exist. The line chart groups by month, so a 30-day view has only one or two points. | `home.blade.php` |
| D-26 | **The "Incomplete Projects" list shows the furthest due dates first,** which is the opposite of what is useful. | `Home` |
| D-27 | *Unverified.* **Filters may escape the owner scope or not apply.** `whereNull(...)->orWhere(...)` is ungrouped, and several tables return Collections while their filters expect a query builder. | `*Table` |
| D-28 | *Unverified.* **Export may not work on most tables.** Only two tables use the `WithExport` trait. | `*Table` |

### Quality and maintenance

| ID | Issue |
|---|---|
| D-29 | Three near-identical upload implementations and two near-identical task implementations. |
| D-30 | Logic lives in UI components, with no policies, form objects, or enums. Priority is a magic number. |
| D-31 | Performance: Collection data sources load every row, relations are lazy-loaded per row, and counters load full relations. |
| D-32 | The dashboard uses MySQL-only `DATE_FORMAT`, which prevents running tests on SQLite. |
| D-33 | Dead code: `Administrator` middleware, `team_id` and `exists:teams,id`, imports of nonexistent `Users` and `Teams` components, Sanctum, `verified`, unused packages, and an unused `SideBarUserDetails` state. |
| D-34 | Accessibility: modals use a hidden checkbox and labels, so they have no focus management or Escape handling. Icon-only buttons rely on tooltips. |
| D-35 | Page visibility is hidden by inline style until DOM ready to mask a theme flash. |
| D-36 | Fonts: Futura is requested from Google Fonts, which does not provide it, and Hedvig Letters Serif is loaded but unused. |
| D-37 | Deployment: CI runs no tests, `composer require` is used as a workaround in CI, dev dependencies are installed in production, and PHP versions differ across the README, CI, and Sail. |
| D-38 | Repository hygiene: `.DS_Store` files are committed and `composer.json` still has the skeleton name. |

### Product gaps

These are not bugs. [Part 5](#part-5-decisions) records which ones the rebuild includes.

- Client and project detail pages show counts but do not list the related projects or issues, so there is no way to navigate to them.
- There is no unread notification indicator.
- There is no password reset, even though mail is configured.
- Sub-resource deletes have no confirmation.

---

## Part 3: Rebuild specification

### 3.1 Principles

1. **Feature parity first.** Everything in Appendix A ships, apart from anything explicitly dropped in Part 5.
2. **Authorize on the server, always.** Livewire public properties are client-controlled input.
3. **No UI dependencies.** All markup, CSS, table behavior, modals, and charts are ours.
4. **One implementation per concept.** Tasks, tables, and modals each have one implementation.
5. **Use framework conventions** wherever they exist, and add abstractions only where duplication is real.

### 3.2 Target stack

Versions were checked against Packagist and npm on 2026-11-04. At scaffold time, use the latest stable releases.

| Concern | Choice | Notes |
|---|---|---|
| Framework | Laravel 13 (v13.40.0) | Requires PHP `^8.3` |
| PHP | 8.4.1 or later | Required by Pest 5 and its Symfony dependencies. Local is 8.5.6. |
| UI | Livewire 4 (v4.4.8) | |
| Client behavior | Alpine 3, **bundled with Livewire** | Do not install it separately; a second copy causes conflicts |
| Styling | Hand-written CSS through Vite | No Tailwind, Bootstrap, DaisyUI, or component libraries |
| Tables | Custom (see 3.8) | Replaces PowerGrid |
| Charts | Custom inline SVG (see 3.9) | Replaces livewire-charts and ApexCharts |
| Export | `openspout/openspout` | Not a UI dependency |
| Database | MySQL 8 locally and in production, SQLite in memory for tests | All queries must be portable |
| Queue, cache, session | `database` driver | Avoids Redis; needs a queue worker for email |
| Tests | Pest 5 | Requires PHP `^8.4` |
| Code style | Laravel Pint | Enforced in CI |
| Static analysis | Larastan | Enforced in CI |
| Local environment | Laravel Sail with MySQL 8.4 and PHP 8.4 | Matches production and the CI MySQL job |

Excluded: Sanctum, Breeze, Jetstream, and every package that ships its own CSS. Hand-written auth is about one component and is simpler than adapting a Tailwind-based starter kit.

### 3.3 Architecture

```
app/
├── Actions/          Multi-step writes (DeleteClient, ResetDemoData)
├── Enums/            IssuePriority, NotificationEvent
├── Jobs/             Scheduled notification and demo jobs
├── Livewire/
│   ├── Concerns/     WithDataTable
│   ├── Forms/        ClientForm, ContactForm, ProjectForm, IssueForm, TaskForm
│   ├── Clients/      Index, Show
│   ├── Projects/     Index, Show
│   ├── Issues/       Index, Show
│   ├── Tasks/        Manager (shared by projects and issues)
│   └── Contacts/     Manager
├── Models/
│   └── Concerns/     BelongsToUser (owner scope), HasTasks
└── Policies/         ClientPolicy, ProjectPolicy, IssuePolicy, ...
```

**Conventions**

- Components stay thin: they authorize, delegate validation to a Form object, and call a model or Action.
- Components use class-based files unless Livewire 4's single-file format proves simpler on a first pass. Pick one format and use it everywhere.
- Models declare `$fillable`, typed relations, casts, and named scopes (`incomplete()`, `unresolved()`, `ownedBy()`). There is no `$guarded = []`.
- Relation methods use camelCase (`incompleteTasks`), not snake_case.
- The application enables `Model::shouldBeStrict()` outside production, so lazy loading and missing attributes fail in development and tests. This would have caught D-10 and D-11.

### 3.4 Authorization (fixes D-1, D-2, D-3)

Every action must satisfy the following, and each has a test.

1. Policies exist for `Client`, `Project`, `Issue`, `UserNotification`. Tasks and contacts authorize through their parent.
2. Every Livewire action that reads or writes a record calls `$this->authorize()`, or resolves the record through the owner's relation, for example `auth()->user()->clients()->findOrFail($id)`.
3. Record identifiers held in public properties use `#[Locked]`.
4. New records get `user_id` from the relation, never from input. Updates never change `user_id`.
5. `client_id` and `project_id` validate with `Rule::exists(...)->where('user_id', auth()->id())`.
6. An issue's project must belong to the selected client, enforced by a validation rule. Changing the client clears the project.
7. A record owned by someone else returns 404 on routes and 403 on actions, and both behaviors are tested. Returning 404 avoids disclosing that a record exists.

Each policy test suite includes "user B cannot view, edit, delete, toggle, or download user A's record" for every action on every resource.

### 3.5 Data model

Migrations are fresh and follow Laravel naming. Foreign keys cascade as they do today.

| Table | Change from current |
|---|---|
| `users` | `demo` renamed to `is_demo`, **default `false`**. Add `email_notifications` boolean, default `true`. |
| `clients` | Add index `(user_id, active)` |
| `client_contacts` | Renamed from `clients_contacts`. `email` stays required. |
| `projects` | Add indexes `(user_id, completed_date)` and `(user_id, due_date)` |
| `issues` | Add index `(user_id, resolved_date)`. `priority` cast to `IssuePriority`. |
| `tasks` | **New polymorphic table** replacing `projects_tasks` and `issues_tasks`: `taskable_type`, `taskable_id`, `title`, `description`, `start_date`, `due_date`, `completed_date`. Issue tasks do not use the date columns, and their forms do not show them. |
| `user_notifications` | `read` boolean replaced by nullable `read_at`. Add nullable `subject_type`, `subject_id` so a notification can link to its record. The old `subject` text column becomes `title`, so that it does not collide with the `subject` relation. |
| `notification_log` | **New**, replacing three dedupe tables: `user_id`, `subject_type`, `subject_id`, `event`, `created_at`, with a unique index on `(subject_type, subject_id, event)` |

`clients`, `client_contacts`, `projects`, `issues`, `tasks`, and `user_notifications` each have a unique `uuid` column, generated when a record is created. Record URLs and URL filters use the uuid in place of the id, so `/clients/{client}` resolves by uuid and a numeric id returns a 404. Livewire actions on contacts, tasks, and notifications also take the uuid. The integer `id` stays the primary key, and foreign keys keep using it.

Polymorphic relations use a `Relation::enforceMorphMap()` with short aliases (`client`, `project`, `issue`, `task`), so class renames never affect stored data. Database foreign keys cannot cover polymorphic columns, so deletion of children is handled in code (see 3.7).

**Enums**

```php
enum IssuePriority: int
{
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;

    public function label(): string;
    public function staleAfterDays(): int; // 30, 14, 7, 3
}
```

`NotificationEvent` is a string enum: `issue_stale_low`, `issue_stale_medium`, `issue_stale_high`, `issue_stale_critical`, `project_due`, `project_overdue`, `task_due`, `task_overdue`.

Status is derived from dates through accessors and scopes (`isCompleted`, `isOverdue`). SQLite stores date casts with a time and MySQL does not, so date scopes compare with half-open ranges instead of `<=`. Date columns are cast to immutable dates, and a small formatter renders missing dates as a caller-specified label (fixes D-16).

### 3.6 Validation

Form objects hold the rules. Where a rule differs from today, that is deliberate.

| Field | Rules |
|---|---|
| Client `name` | required, string, max 255 |
| Client `description` | required, string, max 65535 |
| Contact `name`, `title` | required, string, max 255 |
| Contact `email` | required, email, max 255 |
| Contact `phone` | nullable, string, max 30 |
| Contact `phone_ext` | nullable, string, max 5 |
| Project `name` | required, string, max 255 |
| Project `description` | required, string |
| Project `client_id` | required, owned client |
| Project `start_date` | nullable, date |
| Project `due_date` | nullable, date, `after_or_equal:start_date` |
| Issue `name`, `description` | required, as for project |
| Issue `priority` | required, `Rule::enum(IssuePriority::class)` |
| Issue `client_id` | required, owned client |
| Issue `project_id` | nullable, owned project of the selected client |
| Task `title` | required, string, max 255 |
| Task `description` | required, string |
| Task dates | as for project |
| User `name` | required, string, max 255 (the old minimum of 6 rejects valid short names) |
| User `email` | required, email, max 255, unique, requires current password |
| User `title` | nullable, string, max 255 |
| New password | `Password::defaults()` (min 8), confirmed, requires current password |

Client and project selects list active clients and incomplete projects **plus the record's current value**, so editing never drops an existing link (fixes D-18).

### 3.7 Deleting records

File uploads are removed from the application (see Part 5), so defects D-7, D-21, and D-22 no longer apply. No `uploads` table, storage disk, upload form, or download route exists in the rebuild.

Parent deletes go through Actions (`DeleteClient`, `DeleteProject`, `DeleteIssue`) that run in a transaction and delete polymorphic tasks through Eloquent, since database foreign keys cannot cascade to them.

Deleting a project, issue, or task also removes its `notification_log` rows. Its notifications stay in the user's list without a link.

### 3.8 Data tables (replaces PowerGrid)

`WithDataTable` is a Livewire trait, paired with an `<x-table>` Blade component family.

| Capability | Implementation |
|---|---|
| Query | Each table defines `query(): Builder`, always a query rather than a Collection, always owner-scoped, with eager loads and `withCount` |
| Search | Debounced text input, `#[Url]`, across declared columns, grouped in a closure |
| Sort | Allowlisted columns, `#[Url]`, `aria-sort` on headers |
| Filters | Declared per table; each filter's clause is wrapped in its own group, so `OR` cannot escape the owner scope (fixes D-27) |
| Pagination | Server-side, per-page selector of 10, 25, 50, 100, with a record count |
| Expandable rows | `<details>`-style row with a proper `aria-expanded` button, rendered with escaped text and `white-space: pre-line` |
| Row actions | Icon buttons with `aria-label` and a text tooltip |
| Export | CSV and XLSX of the current filtered set, written through OpenSpout and sent through Livewire's download response. Rows are read with `lazyById()`, so files are in ID order rather than the on-screen sort. Export columns are declared per table. CSV cells that look like formulas are prefixed with an apostrophe. |
| Empty state | A per-table message with an action where useful |
| Loading | `wire:loading` with a subtle table state |
| Responsive | Columns collapse to stacked rows below a breakpoint |

Each table exists for: clients, contacts, projects, project tasks, issues, issue tasks, and notifications. All columns, filters, and defaults from section 1.6 are preserved.

**Related lists (new):** the client detail page also lists that client's projects and issues, and the project detail page lists that project's issues. They reuse the project and issue tables, scoped to the parent.

### 3.9 Charts (replaces livewire-charts)

Blade components render server-side inline SVG and use Alpine only for tooltips. There is no chart library.

- `<x-chart.line>` shows Projects vs Issues with two series.
- `<x-chart.bars>` shows Issue Priorities. Horizontal bars replace the old pie, because close values are hard to compare as slices.
- Both use design tokens, so they follow the theme automatically. They include a text alternative or data table for screen readers.

**Grouping (fixes D-25, D-32):** Grouping happens in PHP or portable SQL. Bucket size follows the timeframe: weekly for 30 and 90 days, monthly for 180 and 365. Empty buckets are filled with zero so that lines stay continuous. Each chart shows its own empty state based on its own data. Buckets are aligned to the end of the timeframe, and a partial first or last bucket shows its exact date range in the tooltip.

### 3.10 Dashboard

The same sections as section 1.6, with these changes:

- All queries live in one `DashboardMetrics` class, tested on its own.
- The timeframe is allowlisted to 30, 90, 180, and 365, and stored with `#[Url]`.
- "Incomplete Projects" sorts by soonest due date first, with undated projects last (fixes D-26).
- A stat tile with an empty prior period says "No prior data" instead of showing 0%, which would read as no change.
- Percent change displays "No prior data" when the prior period is empty, rather than `0% Change`.

### 3.11 Notifications

Jobs keep the current schedule. Add `withoutOverlapping()` and `onOneServer()`. Rows are processed with `chunkById()`. `SendIssueNotifications`, `SendProjectNotifications`, and `SendTaskNotifications` run inside the scheduler rather than on the queue, so the overlap lock covers the whole run and in-app notifications do not depend on a queue worker. Only the emails are queued.

A shared `Notifier::once($subject, $event, $user, $title, $message)` writes the notification and the log row in a transaction. The unique index makes it safe to run repeatedly.

| Rule | Behavior |
|---|---|
| Stale issue | Unresolved and open longer than `IssuePriority::staleAfterDays()`, once per priority |
| Due reminder | Incomplete and due **within the next N days**, once. N is `config('proma.due_reminder_days')`, and the message uses the same value (fixes D-12). |
| Overdue | Incomplete and past due, once |

Task messages use `title` (fixes D-11). Strict mode prevents this class of bug.

The Issue Resolved, Project Completed, and Project Task Completed notifications are dropped, since the user has just performed that action.

Interface additions: each notification links to its record when one exists, `read_at` replaces the boolean, and the sidebar shows the unread count. Task notifications link to the parent project, since tasks have no page. A notification read while the page is open stays in the Unread filter until the page reloads, so that expanding one does not remove it from view.

**Email (new):** each in-app notification also sends one email.

- Mail is a queued Laravel notification, dispatched after the database transaction commits. A mail failure never loses the in-app notification.
- Settings has an on/off preference (`users.email_notifications`), default on, and every email links to the record and to that setting.
- Demo users never receive email, whatever the preference says.
- Templates are custom Blade with inline styles, not a mail UI package.
- Local development uses the `log` mailer, and tests use `Notification::fake()`.

### 3.12 Demo mode (fixes D-9, D-13, D-14, D-15)

- `is_demo` defaults to `false`.
- Demo users have `email_notifications` off.
- `ResetDemoData` runs nightly in a transaction: it deletes demo data, resets the profile and password of each demo user to known values, and reseeds **each** demo user. It then generates that user's notifications right away, so the account is complete before the nightly notification jobs run.
- `DemoUserSeeder` creates the demo account from `PROMA_DEMO_EMAIL` and `PROMA_DEMO_PASSWORD`. It refuses to touch a real account with the same email. Run it on demand in production with `php artisan db:seed --class=DemoUserSeeder --force`.
- It refuses to run on any user where `is_demo` is false, and a test proves real users' data survives.
- A `DemoDataset` class creates a realistic set: about 8 clients with contacts, about 15 projects with tasks, and about 30 issues with tasks. These counts are much lower than the current maximum of 1,500 issues per night.
- Dates are spread around today, so the dashboard and notifications have meaningful content.
- `fakerphp/faker` moves to `require` so production can run this with `--no-dev`.
- `UserPolicy` blocks demo users from changing their email, password, or notification preference.
- The demo login box appears only when `config('proma.demo.enabled')` is true, and shows credentials from configuration. This is how the demo stays optional.

### 3.13 Authentication (fixes D-5, D-6, D-23)

- A `Login` page under `guest` middleware; authenticated visitors are redirected home.
- Login is rate limited by email and IP to 5 attempts per minute, with a generic failure message.
- The session is regenerated on login and the session and CSRF token are invalidated on logout. Logout requires `auth`.
- Application routes use `auth` only. `auth:sanctum` and `verified` are removed.
- `php artisan proma:user:create` creates a real user with a prompted password, replacing the hardcoded seeder account (fixes D-8).
- Seeders contain only demo data, and only run in non-production or on demand.
- Password reset (new) uses Laravel's password broker with custom pages under `guest` middleware. Requests are throttled, the response never reveals whether an email exists, and demo accounts cannot reset.
- The reset notification is queued, so response time does not reveal whether an email exists, and encrypted, because its payload holds the plain token. Sending it needs a queue worker.
- Changing a password ends the user's other sessions through `AuthenticateSession` (`auth.session`).
- The login page background is custom CSS or SVG using the palette. The video is removed.

Baseline for any deployment: `APP_DEBUG=false`, secure session cookies, and security headers including a Content Security Policy compatible with Livewire and Alpine. Phase 1 tested this in a browser: the application runs on Livewire's CSP-safe build (`csp_safe` in `config/livewire.php`) with a nonce-based policy and no `unsafe-inline` or `unsafe-eval`. This has three consequences:

- Alpine logic lives in registered `Alpine.data()` components, with simple expressions in templates.
- Server-rendered HTML must not use `style` attributes.
- Livewire's navigation progress bar is disabled, because its styles have no nonce.

`AddSecurityHeaders` is global middleware, so responses produced outside the `web` group, such as route 404s and maintenance 503s, also carry these headers.

### 3.14 Routes

URLs are preserved. Names move to Laravel resource conventions.

| URL | Name | Component |
|---|---|---|
| `/login` | `login` | `Auth\Login` |
| `/logout` (POST) | `logout` | `Auth\LogoutController` |
| `/forgot-password` | `password.request` | `Auth\ForgotPassword` |
| `/reset-password/{token}` | `password.reset` | `Auth\ResetPassword` |
| `/privacy-policy` | `privacy` | `PrivacyPolicy` |
| `/` | `dashboard` | `Dashboard` |
| `/clients` | `clients.index` | `Clients\Index` |
| `/clients/{client}` | `clients.show` | `Clients\Show` |
| `/projects` | `projects.index` | `Projects\Index` |
| `/projects/{project}` | `projects.show` | `Projects\Show` |
| `/issues` | `issues.index` | `Issues\Index` |
| `/issues/{issue}` | `issues.show` | `Issues\Show` |
| `/notifications` | `notifications.index` | `Notifications\Index` |
| `/settings` | `settings` | `Settings` |

Navigation uses `wire:navigate` for instant page changes. No API routes are defined.

### 3.15 Front end and CSS

#### CSS organization

Plain CSS, compiled and bundled by Vite with native `@import`. There is no preprocessor and no utility framework.

```
resources/css/
├── app.css            Layer order and imports
├── base/
│   ├── tokens.css     Colors, spacing, type, radii, shadows, motion
│   ├── reset.css
│   └── typography.css
├── layout/            shell, sidebar, header, page, grid
├── components/        button, card, badge, alert, form, table, dialog,
│                      stat, tabs, tooltip, chart, pagination, toast
└── pages/             login, error
```

**Rules**

- Use `@layer tokens, reset, base, layout, components, pages, utilities;` to keep specificity predictable and avoid `!important`.
- Class names follow a BEM-style pattern with component prefixes, such as `.btn`, `.btn--primary`, and `.card__title`.
- Blade components own their class names; views should rarely write them directly.
- Use only tokens for colors, spacing, type sizes, and radii. Hard-coded color values appear only in `tokens.css`.
- Use modern native features: custom properties, nesting, container queries, logical properties, `color-mix()`.
- Mobile-first; breakpoints are defined once and documented in `tokens.css`.
- A CSS size budget of about 40 KB minified applies, reviewed at launch.

#### Theming

- The interface does not carry over the old DaisyUI look. It is neutral and dense: zinc grays, near-black primary actions (near-white in dark mode), indigo only for links and focus, 1px borders instead of shadows, small radii, 14px base text, and rectangular badges. Tokens are semantic (`--color-surface`, `--color-text`, `--color-primary`) and are redefined under `[data-theme="light"]` and `[data-theme="dark"]`.
- A small inline script in `<head>` applies the stored theme before first paint, which replaces the hidden-body workaround (fixes D-35).
- The default follows `prefers-color-scheme` when nothing is stored.
- The status colors must be checked for WCAG AA contrast against every surface in both themes, and adjusted as needed.

#### Fonts (fixes D-36)

A self-hosted variable `woff2` of Inter for the interface, falling back to the system font stack. The logo is a small SVG mark with a text wordmark. No requests go to Google Fonts, which also keeps the privacy policy simple.

#### Blade components

| Group | Components |
|---|---|
| Layouts | `layouts.app`, `layouts.guest`, `layouts.error`, `page-header` |
| Inputs | `input`, `textarea`, `select`, `checkbox`, `toggle`, `field-error` |
| Elements | `button`, `badge`, `card`, `stat`, `alert`, `timestamps`, `icon`, `logo` |
| Overlay | `dialog`, `confirm-dialog` |
| Data | `table`, `table.head`, `table.row`, `pagination` |
| Charts | `chart.line`, `chart.bars` |

Icons are one `<x-icon name="…">` component backed by SVG files. Only icons in use are ported, which removes leftovers such as `teams`, `users`, and `admin`.

#### Alpine responsibilities

Alpine handles only client-side state: the theme toggle, sidebar drawer, dialog open and close, inline edit mode, toasts, and chart tooltips. Reusable behavior is registered through `Alpine.data()` in `resources/js/`, loaded on the `alpine:init` event.

#### Dialogs (fixes D-34)

Dialogs use the native `<dialog>` element with `showModal()`, which gives focus trapping, Escape handling, and a backdrop. An Alpine wrapper opens them from Livewire events.

Consistent behavior after saving (fixes D-24): validation errors stay inside the dialog; on success the dialog closes, the form resets, the list refreshes, and a toast confirms.

**Confirmation:** Every delete uses a confirm dialog. Parent deletes state what cascades.

#### Accessibility

The target is WCAG 2.2 AA:

- Semantic landmarks, one `h1` per page, and a skip link.
- Every input has a label, and errors are linked through `aria-describedby`.
- Visible focus states and keyboard access for all actions.
- Icon-only buttons have accessible names.
- `prefers-reduced-motion` is respected.
- Status is never conveyed by color alone.

### 3.16 Testing

Target: at least 90% line coverage on `app/`, excluding framework scaffolding. Tests run on in-memory SQLite, plus a MySQL job in CI to catch driver differences.

| Suite | Scope |
|---|---|
| Authorization | Every action on every resource, for owner, other user, guest, and demo user |
| Feature | Each page and form, all validation rules, status toggles, cascades |
| Tables | Search, sort, filter, pagination, and export, including that filters cannot widen the owner scope |
| Jobs | All rules, idempotency, overdue windows, using `travelTo` |
| Email | Sent once per notification, opt-out respected, demo users excluded, password reset flow |
| Demo | Reset restores the profile and reseeds, and non-demo users are untouched |
| Dashboard | Metrics and chart bucketing for each timeframe |
| Regression | One named test for every applicable defect in Part 2 |
| Architecture | Pest `arch()`: no `dd`, no `$guarded = []`, strict typing |
| Security | No `{!!` in views; asserts that stored HTML is escaped |

A small Pest browser suite is optional and covers login, one dialog flow, and the theme toggle.

### 3.17 CI (fixes D-37)

The application is not currently deployed, so the rebuild includes CI only.

**CI (`.github/workflows/ci.yml`)**, on pushes and pull requests:

1. Pint in test mode and Larastan.
2. Pest with coverage on SQLite, and a MySQL job.
3. `npm ci` and `npm run build`.

The old deployment scripts and workflow are not carried over. A deployment pipeline is a separate later task. Whichever one is chosen will need PHP per 3.2, a cron entry for `php artisan schedule:run`, a supervised `php artisan queue:work`, working SMTP, and `composer install --no-dev`.

### 3.18 Project conventions

- A `README.md` with setup, test, and deploy instructions.
- A `CLAUDE.md` with architecture and conventions, so later work stays consistent.
- `composer.json` renamed to `cdterry87/proma`, with `composer run test` and `composer run lint` scripts.
- `.gitignore` covers `.DS_Store`, and any committed copies stay out of the new history.

---

## Part 4: Migration plan

### 4.1 Branch reset

The application is not deployed and its data does not need to be preserved, so there is no importer or cutover. The archive follows the existing `archive-*` naming. `archive-master-2024` is free, and matches the year of the last commit.

> **Warning:** the old `production` branch still has a workflow that deploys over SSH on push, and the old `deploy.sh` merges `master` into `production`. Do not push to `production` or run that script. Both branches can be left as they are, and removing them and the repository's SSH secrets is a separate cleanup.

1. Commit `docs/spec.md` to `master`.
2. Create and push the archive from that commit:

   ```bash
   git switch -c archive-master-2024
   git push -u origin archive-master-2024
   git switch master
   ```

3. Confirm the archive exists on GitHub and matches `master`.
4. Rename the branch and make it the default:

   ```bash
   git branch -m master main
   git push -u origin main
   gh repo edit --default-branch main
   ```

5. Delete the remote `master` once `main` is the default.
6. Remove everything except `docs/`, commit, and scaffold the new application in the same directory.
7. Build in phases (4.2).

### 4.2 Build phases

Each phase ends with passing tests and is independently reviewable.

| Phase | Scope |
|---|---|
| 0 | Archive, rename to `main`, clear it, scaffold Laravel 13 and Livewire 4, CI, Pint, Larastan, Pest |
| 1 | Design system: tokens, CSS layers, layouts, Blade components, dialogs, theme, a private style reference page |
| 2 | Auth, password reset, app shell, error pages, privacy policy, settings |
| 3 | Data model, enums, policies, factories, authorization test scaffolding |
| 4 | `WithDataTable`, export, and clients with contacts as the first complete vertical |
| 5 | Projects, issues, and the shared tasks manager |
| 6 | Dashboard, metrics, and charts |
| 7 | Notifications, email, jobs, and scheduler |
| 8 | Demo mode and demo dataset |
| 9 | Accessibility and performance review, final parity check |

---

## Part 5: Decisions

Decided on 2026-11-04.

| # | Topic | Decision |
|---|---|---|
| 1 | Production data | Start fresh. No importer. |
| 2 | Deployment | Not deployed. CI only; a pipeline is a later task. |
| 3 | Demo mode | Keep, behind configuration. |
| 4 | Where to build | Directly on the main branch, after archiving. |
| 5 | Branch name | Rename `master` to `main`. |
| 6 | Tasks | One polymorphic table. |
| 7 | Issue task dates | None. Issue tasks generate no notifications. |
| 8 | Due reminder window | 10 days, configurable. |
| 9 | Completed and resolved notifications | Dropped. |
| 10 | Password reset | Included. |
| 11 | Email notifications | Included, with a per-user opt-out. |
| 12 | Related lists on detail pages | Included. |
| 13 | Unread notification badge | Included. |
| 14 | Confirmation on sub-resource deletes | Included. |
| 15 | Login background | Custom CSS or SVG. Video removed. |
| 16 | Default theme | Follow the operating system when nothing is stored. |
| 17 | `warning` color | Amber instead of purple. |
| 18 | File uploads | Removed entirely, for clients, projects, and issues. This is a portfolio project, and the test server will not store user files. |
| 19 | Contact email | Required. |
| 20 | Descriptions | Required on forms, as today. |
| 21 | Credit and privacy policy | Keep the credit. Update the privacy text for email use and the removal of Google Fonts, with real contact details and a new effective date. |
| 22 | Test framework | Pest. |

---

## Appendix A: Feature parity checklist

Acceptance criteria for cutover.

**Authentication and account**

- [ ] Login with remember-me and redirect to intended page
- [ ] Password reset by email
- [ ] Logout with confirmation message
- [ ] Demo credentials displayed on login when demo is enabled
- [ ] Edit name, email, title, and password separately
- [ ] Sidebar reflects profile changes without a reload

**Dashboard**

- [ ] Timeframe selector for 30, 90, 180, and 365 days
- [ ] Four stat tiles with period-over-period change
- [ ] Incomplete projects and unresolved issues lists, limited to five, linked
- [ ] Projects vs Issues line chart
- [ ] Issue Priorities chart
- [ ] Empty states

**Clients**

- [ ] List with counts, search, sort, active filter, expandable description, export, pagination
- [ ] Create, edit, and delete with cascade and confirmation
- [ ] Detail page with status, details, stats, and contacts
- [ ] Contacts: list, search, sort, filter, export, create, edit, delete, active flag
- [ ] Related projects and issues listed on the detail page

**Projects**

- [ ] List with client, dates, filters, search, sort, expandable description, export, pagination
- [ ] Create, edit, delete, and complete or reopen
- [ ] Client select limited to active clients
- [ ] Detail page with status, details, stats, and tasks
- [ ] Tasks: list, filters, search, sort, export, create, edit, complete or reopen, delete
- [ ] Related issues listed on the detail page

**Issues**

- [ ] List with priority, client, project, filters, search, sort, expandable description, export, pagination
- [ ] Create, edit, delete, and resolve or reopen
- [ ] Project select depends on selected client
- [ ] Detail page with status and priority badges, details, stats, and tasks
- [ ] Tasks: list, filter, search, sort, export, create, edit, complete or reopen, delete

**Notifications**

- [ ] Stale issue, due, and overdue rules from section 1.6 generated once per record
- [ ] Each notification also emailed, with a per-user opt-out
- [ ] Unread count in the sidebar
- [ ] List, search, sort, read filter, export
- [ ] Expanding a notification marks it as read
- [ ] Mark all as read
- [ ] Scheduled at the documented times

**Demo**

- [ ] Nightly reset of demo users' data
- [ ] Demo restrictions enforced
- [ ] Real users' data never affected

**Application shell**

- [ ] Responsive sidebar with active state and drawer on mobile
- [ ] Dark and light themes that persist without a flash
- [ ] Flash and toast messages for all four severities
- [ ] Show More and Show Less on long detail values
- [ ] Confirmation before every delete
- [ ] Custom 403, 404, and 503 pages
- [ ] Privacy policy in both layouts
- [ ] Favicons and web manifest

**Constraints**

- [ ] No Tailwind, DaisyUI, Bootstrap, PowerGrid, or other UI library in `composer.lock` or `package-lock.json`
- [ ] Alpine comes only from Livewire's bundle
- [ ] Every defect in Part 2 is fixed or explicitly waived
- [ ] CI passes with coverage at or above target
- [ ] WCAG 2.2 AA review completed
