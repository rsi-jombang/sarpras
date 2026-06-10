# Sarpras RS — Inventory Management System

Laravel 13.x + PHP 8.3+ app for hospital facility (sarana/prasarana) inventory management. SQLite (default), Bootstrap 5, SCSS, Vite.

## Commands

| Context | Command | Notes |
|---|---|---|
| Full setup | `composer run setup` | install → copy .env → key:gen → migrate → npm i → vite build |
| Dev servers | `composer run dev` | Runs artisan serve + queue:listen + pail + vite concurrently |
| Run tests | `composer run test` | Runs `config:clear` then `php artisan test` |
| Single test | `php artisan test --filter=TestName` | |
| Vite build | `npm run build` | |
| Vite dev | `npm run dev` | |
| Code style | `./vendor/bin/pint` | Laravel Pint |

## Architecture

- **`app/Services/`** — Core business logic (StockService: FIFO fulfillment, PO receive, stock opname close). All registered as singletons in `AppServiceProvider`.
- **`app/Enums/`** — Native PHP enums cast via Laravel. `UserRole::Admin` bypasses all gates.
- **Dual routing** — Web routes (`routes/web.php`) for page rendering; `/api/*` prefixed routes for DataTables/JSON responses.
- **Items use POST for update** (`routes/web.php:68`) because of file upload support.
- **Seeder order matters**: Unit → User → Category → Item (`database/seeders/DatabaseSeeder.php:15`).
- **`RoleMiddleware`** exists at `app/Http/Middleware/RoleMiddleware.php` but is **not active** — routes use Laravel Gates/Policies instead.
- **Gate bypass**: Admin role skips all policy checks via `Gate::before` in `AppServiceProvider`.

## Conventions

- **SCSS entry**: `resources/scss/app.scss` (not `resources/css/`).
- **JS entry**: `resources/js/app.js` imports Bootstrap 5, dashboard script, and helpers.
- **`.npmrc`** has `ignore-scripts=true` — run `npm i --ignore-scripts= false` if postinstall scripts are needed.
- **`.env` defaults**: DB_CONNECTION=sqlite, SESSION_DRIVER=database, QUEUE_CONNECTION=database, CACHE_STORE=database.
- **Testing**: PHPUnit (not Pest), uses in-memory SQLite (`phpunit.xml:27`). No special services required.
- **UI theme**: Bootstrap 5 with custom "Nexus Admin" SCSS theme. Uses CSS custom properties for light/dark mode (see `layout_guide.md`).
