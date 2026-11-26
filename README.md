# Harbour.Space HelpDesk

A Laravel web application where the students and teachers of Harbour.Space University in Barcelona report IT and campus problems, such as a broken projector or Wi-Fi that does not work.

## What the app does

Every problem is a **ticket**. A ticket has a title, a category, a location, a description, a priority (normal, high, urgent) and a status (open, in progress, solved, closed).

- **Students** create tickets, follow their status and reply to the IT staff. They can edit or delete a ticket while it is still open, and close it once it is solved.
- **Teachers** can do the same, and they also choose the priority of their tickets.
- **Administrators** (the IT and campus staff) work in the admin panel:
    - a dashboard with the number of tickets per status and the urgent and high priority tickets,
    - a list of all tickets with a search and filters, where they change the status and the priority and reply to the user,
    - the categories (create, edit, delete),
    - the users (change the role and approve new accounts).

The email address decides what happens when somebody registers:

- `name@student.harbour.space`: the account is a **student** and can be used right away.
- `name@teacher.harbour.space`: the account is a **teacher** and can be used right away.
- Any other address: the account is created, but it can only log in after an admin approves it on the **Users** page of the admin panel.

The two domains are set in `config/helpdesk.php` (`HELPDESK_STUDENT_EMAIL_DOMAIN` and `HELPDESK_TEACHER_EMAIL_DOMAIN` in `.env`).

## Built with

- Laravel 13 on PHP 8.5, with Blade views
- Tailwind CSS and Alpine.js, built with Vite
- Pest for the tests
- SQLite on your computer, PostgreSQL ([Neon](https://neon.tech)) on Vercel

## Run it on your computer

### Requirements

- PHP 8.5 and [Composer](https://getcomposer.org)
- [Node.js](https://nodejs.org) 22 or newer, with npm
- Git

[Laravel Herd](https://herd.laravel.com) installs PHP and Composer for you on Windows and macOS. The database is a SQLite file, so you do not need to install a database server.

### Installation

```bash
git clone https://github.com/xkhaliil/HelpDeskHarbourSpace.git
cd HelpDeskHarbourSpace

composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate

php artisan migrate:fresh --seed
php artisan storage:link

php artisan serve
```

Then open http://127.0.0.1:8000.

What each step does:

- `composer install` and `npm install` download the PHP and JavaScript packages.
- `npm run build` builds the CSS and JavaScript. Without it, pages fail with "Unable to locate file in Vite manifest". While you work on the views, you can run `npm run dev` in a second terminal instead.
- `cp .env.example .env` creates your settings file. On Windows Command Prompt, use `copy .env.example .env`.
- `php artisan key:generate` writes an `APP_KEY` into `.env`. **Without this key every page shows a 500 error.**
- `php artisan migrate:fresh --seed` creates the SQLite database (`database/database.sqlite`), its tables and the demo data. Run it again whenever you want to start over with fresh demo data.
- `php artisan storage:link` makes the uploaded ticket screenshots (saved in `storage/app/public/screenshots`) visible in the browser. The app also works without it, but then the screenshots do not show.

If you use Laravel Herd, you can skip `php artisan serve`: put the project in your Herd folder and open `http://<folder-name>.test`.

### Demo accounts

All seeded accounts use the password `password`.

| Role    | Email                   |
|---------|-------------------------|
| Admin   | `admin@example.com`       |
| Teacher | `teacher@harbour.space` |
| Student | `student@harbour.space` |

These three accounts are already approved. The seeder also creates two accounts that are still waiting for approval (`jordi.puig@gmail.com` and `sofia.martin@harbour.space`), so you can try the approval on the **Users** page of the admin panel. It also creates 5 categories and 40 tickets with replies.

### Tests

```bash
php artisan test
```

## Deploy on Vercel

Vercel runs the app as a Docker container. Everything it needs is in the repository:

- `vercel.json`: tells Vercel to build the `Dockerfile` and send every request to it.
- `Dockerfile`: PHP 8.5 with FrankenPHP. It installs the Composer packages (without the dev packages) and builds the CSS and JavaScript with Vite.
- `Caddyfile`: the web server configuration (serves the `public` folder).

Your `.env` file and your SQLite database are **not** sent to Vercel. Vercel does not keep files between requests, so the deployed app uses an external PostgreSQL database (Neon), and gets its settings from environment variables that you add in the Vercel dashboard. Your local copy keeps using SQLite.

For the same reason, **screenshot uploads are turned off on Vercel** (`HELPDESK_SCREENSHOTS_ENABLED=false` in the `Dockerfile`): an uploaded file would disappear after the request. On your computer they stay on (`HELPDESK_SCREENSHOTS_ENABLED=true` in `.env`).

> [!IMPORTANT]
> The app stores its sessions in the database, so **every page** (even the home page) needs all three of these before it works. If one of them is missing, every page shows **500 Server Error**:
>
> 1. an `APP_KEY` environment variable for the **Production** environment,
> 2. a database connected to the **Production** environment,
> 3. the tables created in that database.
>
> After you add or change an environment variable, you must **redeploy**. A running deployment never sees new variables.

### What you need

- A [GitHub](https://github.com) account with this repository (your own copy or fork)
- A [Vercel](https://vercel.com) account
- The project installed on your computer (see [Run it on your computer](#run-it-on-your-computer)), because you create the tables from there

### Step 1: Import the repository in Vercel

In Vercel, click **Add New → Project** and import the GitHub repository. Vercel reads `vercel.json` and builds the `Dockerfile`, so you do not need to change any build settings.

The first deployment shows **500 Server Error**. That is expected: the key and the database are not set up yet. Steps 2 to 5 fix it.

### Step 2: Add the APP_KEY

Laravel uses the `APP_KEY` to encrypt the cookies and the sessions. Generate a new key on your computer, in the project folder:

```bash
php artisan key:generate --show
```

This prints a key like `base64:Xy3k...=` and does not change your `.env` file. Copy the whole line, including `base64:`.

In Vercel, open your project, go to **Settings → Environment Variables** and add:

| Variable  | Value                                                         | Needed?     |
|-----------|---------------------------------------------------------------|-------------|
| `APP_KEY` | the key you just copied, without quotes                       | **Required** |
| `APP_URL` | the address of the site, like `https://your-project.vercel.app` | Recommended |

Make sure **Production** is selected for both.

### Step 3: Create the database and connect it

1. In your Vercel project, open the **Storage** tab and create a **Neon** (Postgres) database. The free plan is enough.
2. When Vercel asks which environments to connect, select **Production**. You can also select Preview.

Vercel now adds `DATABASE_URL`, `DATABASE_URL_UNPOOLED` and a few other variables to the project. The app reads `DATABASE_URL` by itself (see `config/database.php`), so you do not need to add anything else.

> [!WARNING]
> Do not add a `DB_URL` variable unless you use a database that is not from Neon. When `DB_URL` exists, the app uses it instead of `DATABASE_URL`, so a wrong or placeholder value breaks the connection.

### Step 4: Create the tables and the demo data

The tables are created from **your own computer**. Your local PHP connects to Neon for this one command.

1. In Vercel, open the database in the **Storage** tab and copy the value of **`DATABASE_URL_UNPOOLED`**. Reveal the secret first, so the password is part of the copied value. It is one long line like this:

    ```
    postgresql://neondb_owner:AbC123xyz@ep-cool-name-a1b2c3d4.eu-central-1.aws.neon.tech/neondb?sslmode=require
    ```

    Use the unpooled one: Neon recommends a direct connection for migrations.

2. Open a terminal in the project folder (in VS Code: **Terminal → New Terminal**). Check which terminal you have, because the command is different in each one:

    | The prompt looks like                  | Terminal                    |
    |----------------------------------------|-----------------------------|
    | `PS C:\...\HelpDeskHarbourSpace>`      | Windows PowerShell          |
    | `C:\...\HelpDeskHarbourSpace>`         | Windows Command Prompt      |
    | `user@computer ... $`                  | macOS, Linux or Git Bash    |

3. Run the command for your terminal. Replace `PASTE-DATABASE_URL_UNPOOLED-HERE` with the value you copied, and keep the quotes.

    **PowerShell** (one line):

    ```powershell
    $env:DB_CONNECTION="pgsql"; $env:DB_URL="PASTE-DATABASE_URL_UNPOOLED-HERE"; php artisan migrate --force --seed
    ```

    **Command Prompt** (three lines, one after the other):

    ```bat
    set DB_CONNECTION=pgsql
    set "DB_URL=PASTE-DATABASE_URL_UNPOOLED-HERE"
    php artisan migrate --force --seed
    ```

    **macOS, Linux or Git Bash**:

    ```bash
    DB_CONNECTION=pgsql DB_URL="PASTE-DATABASE_URL_UNPOOLED-HERE" php artisan migrate --force --seed
    ```

    You should see every migration marked `DONE`, then the seeders running.

4. **Close this terminal window** when it is done. The PowerShell and Command Prompt variables stay active until the window is closed, so any later `php artisan` command in it would run against the production database instead of your local one.

> [!CAUTION]
> Never put the connection string in a file of the repository (`.env.example`, the README, ...). It contains the password of your database.

### Step 5: Redeploy

In Vercel, go to **Deployments**, open the **⋯** menu of the newest deployment and click **Redeploy**. Wait until it says **Ready**. Building the Docker image takes a few minutes.

You need this step because the deployment from step 1 was created before the `APP_KEY` and the database variables existed.

### Step 6: Check that it works

Open your site and log in with `admin@example.com` and the password `password`. If you still see **500 Server Error**, go to [Troubleshooting](#troubleshooting-500-server-error).

### Checklist

- [ ] `APP_KEY` added in Vercel, with **Production** selected
- [ ] Neon database created and connected to **Production** (`DATABASE_URL` is in the environment variables)
- [ ] No `DB_URL` variable with a wrong or placeholder value
- [ ] Tables created with `php artisan migrate --force --seed` and the real connection string
- [ ] Redeployed after the last change to the environment variables

### Everything else is already set

The `Dockerfile` sets the other production settings, so you do not add them in Vercel:

| Setting                       | Value      | Why                                                          |
|-------------------------------|------------|--------------------------------------------------------------|
| `APP_ENV`                     | production |                                                              |
| `APP_DEBUG`                   | false      | Visitors see "Server Error" instead of the code and the passwords |
| `DB_CONNECTION`               | pgsql      | Use PostgreSQL. Set `DB_CONNECTION=mysql` in Vercel to use MySQL instead |
| `SESSION_DRIVER`, `CACHE_STORE` | database | Every Vercel instance shares the same sessions and cache   |
| `SESSION_SECURE_COOKIE`       | true       | Cookies are only sent over HTTPS                             |
| `LOG_CHANNEL`                 | stderr     | Errors appear in the **Logs** tab of Vercel                  |
| `HELPDESK_SCREENSHOTS_ENABLED` | false     | Vercel does not keep uploaded files                          |

### Later updates

- Every push to the `main` branch deploys again automatically.
- When you add a new migration, run `php artisan migrate` on your computer as usual, then run the command from step 4 again **without `--seed`**, so the production database gets the new tables too. Running `--seed` again would add a second copy of the demo data.
- After you add or change an environment variable in Vercel, redeploy.

## Troubleshooting: 500 Server Error

Because `APP_DEBUG` is off in production, the page only says "Server Error". The real error is in the Vercel logs:

1. Open your project in Vercel and go to the **Logs** tab.
2. Type `production.ERROR` or `SQLSTATE` in the search box.

Laravel prints a long stack trace for each error, and Vercel shows the newest lines first. The lines you see first (or copy) are usually the **end** of the stack trace, which does not contain the message. The search shows the line with the actual message.

| The error message contains                                          | Cause                                         | Fix |
|---------------------------------------------------------------------|-----------------------------------------------|-----|
| `No application encryption key has been specified`, or `EncryptionServiceProvider` in the stack trace | `APP_KEY` is missing for Production | [Step 2](#step-2-add-the-app_key), then redeploy |
| `SQLSTATE[08006]` ... `"127.0.0.1"` ... `Connection refused`        | The app has no database URL, so it tries a database on its own server | [Step 3](#step-3-create-the-database-and-connect-it), then redeploy |
| `SQLSTATE[42P01]` ... `relation "sessions" does not exist` (or another table) | The database is connected, but the tables were never created | [Step 4](#step-4-create-the-tables-and-the-demo-data). No redeploy needed |
| `password authentication failed`                                    | A `DB_URL` variable has a wrong or old connection string | Fix or delete `DB_URL`, then redeploy |
| `Cannot modify header information - headers already sent`          | A side effect of another error                | Look for the first error of the same request |

If you fixed a variable and the logs still show the same error, check the **deployment ID** (`dpl_...`) of the log lines. If it is the same as before, you have not redeployed yet.

### Errors when running the command of step 4

| You see                                                              | Fix |
|----------------------------------------------------------------------|-----|
| `The filename, directory name, or volume label syntax is incorrect.` | You ran the PowerShell command in Command Prompt. Use the Command Prompt version, or type `powershell` first. |
| `could not translate host name "..."` or `"PASTE-..."`               | The placeholder is still in the command. Paste the real connection string. |
| `could not find driver`                                              | Your PHP has no PostgreSQL driver. Enable the `pdo_pgsql` extension in your `php.ini` (Laravel Herd already has it). |
