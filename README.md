# Harbour.Space HelpDesk

A Laravel web application where the students and teachers of Harbour.Space University in Barcelona report IT and campus problems, such as a broken projector or Wi-Fi that does not work.

Every problem is a **ticket**. Students and teachers create tickets and follow their status (open, in progress, solved, closed). Teachers can also set the priority of a ticket. Administrators (the IT and campus staff) handle all tickets, reply to users and manage the categories and the user roles.

## Requirements

- PHP 8.5 and Composer
- Node.js and npm
- SQLite (the default database in `.env.example`)

## Installation

```bash
git clone https://github.com/xkhaliil/HelpDeskHarbourSpace.git
cd HelpDeskHarbourSpace

composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed

php artisan serve
```

Then open http://127.0.0.1:8000.

The email address decides what happens when somebody registers:

- `name@student.harbour.space`: the account is a **student** and can be used right away.
- `name@teacher.harbour.space`: the account is a **teacher** and can be used right away.
- Any other address: the account is created, but it can only log in after an admin approves it on the **Users** page of the admin panel.

The two domains are set in `config/helpdesk.php` (`HELPDESK_STUDENT_EMAIL_DOMAIN` and `HELPDESK_TEACHER_EMAIL_DOMAIN` in `.env`).

## Demo accounts

All seeded accounts use the password `password`.

| Role    | Email                   |
|---------|-------------------------|
| Admin   | `admin@admin.com`       |
| Teacher | `teacher@harbour.space` |
| Student | `student@harbour.space` |

These three accounts are already approved. The seeder also creates two accounts that are still waiting for approval (`jordi.puig@gmail.com` and `sofia.martin@harbour.space`), so you can try the approval on the **Users** page of the admin panel.

## Tests

```bash
php artisan test
```

## Deploy on Vercel

Vercel runs the app as a Docker container. Everything it needs is in the repository:

- `vercel.json`: tells Vercel to build the `Dockerfile` and send every request to it.
- `Dockerfile`: PHP 8.5 with FrankenPHP. It installs the Composer packages (without the dev packages) and builds the CSS and JavaScript with Vite.
- `Caddyfile`: the web server configuration (serves the `public` folder).

Vercel does not keep files between requests, so the app needs an **external database**.

1. **Create a Postgres database**, for example Neon (free) from the Storage tab of your Vercel project, and copy its connection string (`postgresql://...`).

2. **Create the tables and the demo data** from your own computer:

    ```bash
    DB_CONNECTION=pgsql DB_URL="postgresql://..." php artisan migrate --force --seed
    ```

    In PowerShell:

    ```powershell
    $env:DB_CONNECTION="pgsql"; $env:DB_URL="postgresql://..."; php artisan migrate --force --seed
    ```

3. **Import the GitHub repository in Vercel** and add these environment variables (Project Settings → Environment Variables):

    | Variable  | Value                                                        |
    |-----------|--------------------------------------------------------------|
    | `APP_KEY` | the output of `php artisan key:generate --show`              |
    | `APP_URL` | the address of the site, like `https://your-project.vercel.app` |
    | `DB_URL`  | the Postgres connection string (not needed when Neon already added `DATABASE_URL`) |

    Everything else has a production default in the `Dockerfile`: no debug mode, sessions and cache in the database, logs in the Vercel runtime logs. To use MySQL instead of Postgres, also set `DB_CONNECTION=mysql`.

4. **Deploy.** Every push to the `main` branch deploys again. After changing an environment variable, redeploy.
