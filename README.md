# CampusConnect

Laravel 12 / Livewire campus event app, configured for SQLite and Render Docker hosting.

## Deploy to Render

1. Push this repository to GitHub and create a **Blueprint** in Render using `render.yaml`.
2. Supply the three prompted secrets:
   - `APP_KEY`: generate once with `php artisan key:generate --show`. Keep the same key across deployments.
   - `ADMIN_PASSWORD`: choose a strong password for `admin@campusconnect.test`.
   - `DEMO_PASSWORD`: choose a separate password for the demo members.
3. Deploy. Startup migrates SQLite, seeds data, links uploads, and caches configuration/views. `APP_URL` defaults to Render's assigned HTTPS URL; set it explicitly if using a custom domain.
4. Log in at `/login`. The feed is at `/events`; admin moderation uses the existing event/comment controls (there is no separate admin dashboard).

The blueprint uses a paid Starter web service and a 1 GB persistent disk. SQLite lives at `/var/data/database.sqlite`; uploads live at `/var/data/public`. Render disks require paid services, allow one instance, and are available only at runtime, so migration/seeding run at startup. See https://render.com/docs/disks. Do not remove the disk or change the application key between deploys. For database backups, use SQLite's online backup API rather than copying a live database file.

Mail uses the log driver; configure a real mail provider before relying on password-reset emails. Queue jobs run synchronously, so no worker is needed.

## Seeded accounts and activity

| Account | Email | Password |
| --- | --- | --- |
| Administrator | `admin@campusconnect.test` (or `ADMIN_EMAIL`) | `ADMIN_PASSWORD` |
| Demo member | `demo@campusconnect.test` | `DEMO_PASSWORD` |
| Other members | `member2@campusconnect.test` through `member12@campusconnect.test` | `DEMO_PASSWORD` |

Demo data includes 12 members, 11 Lagos campus events (10 upcoming, one past), 88 likes, 44 comments, and 132 interested/attending RSVPs. Dates are relative to the initial seed date. Existing accounts, passwords, event dates, and interactions are preserved on subsequent deploys. Events naturally become past events over time; fresh future events can be created in the app. Set `SEED_DEMO_DATA=false` to stop adding demo fixtures; this does not delete existing data.

The admin is created only when its email is absent. Later changes to seed password variables do not reset existing passwords or promote existing ordinary users. Use the normal password-reset flow or an explicit administrative password update for existing accounts.

## Local setup

```sh
composer install
npm ci
cp .env.example .env
php artisan key:generate
# Set ADMIN_PASSWORD and DEMO_PASSWORD in .env; set SEED_DEMO_DATA=true.
# Keep DB_CONNECTION=sqlite and optionally set an absolute DB_DATABASE path.
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Use a separate admin password and demo password. No production password is committed to the repository. Seeding does not require Faker or development Composer packages.

## Verification

```sh
php artisan test
npm run build
docker build -t campus-connect .
```
