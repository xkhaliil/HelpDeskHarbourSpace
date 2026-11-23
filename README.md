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
