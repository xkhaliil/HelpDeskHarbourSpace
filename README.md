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
php artisan storage:link

php artisan serve
```

Then open http://127.0.0.1:8000.

`php artisan storage:link` makes the uploaded ticket screenshots (saved in `storage/app/public/screenshots`) visible in the browser. The app also works without screenshots.

Only email addresses of the university domain can register. The domain is set in `config/helpdesk.php` (`HELPDESK_EMAIL_DOMAIN` in `.env`, default `harbour.space`, subdomains like `student.harbour.space` are allowed). New accounts always get the role `student`.

## Demo accounts

All seeded accounts use the password `password`.

| Role    | Email                   |
|---------|-------------------------|
| Admin   | `admin@admin.com`       |
| Teacher | `teacher@harbour.space` |
| Student | `student@harbour.space` |

## Tests

```bash
php artisan test
```
