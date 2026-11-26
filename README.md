# FeelTok

FeelTok is a mobile app for sending short, positive messages linked to six values: Love, Gratitude, Apology, Appreciation, Mindfulness and Thank you. Admins keep the content on these values. This is the prototype for SWE6301 Agile Programming (Team 2).

Repository: https://github.com/Akshat-G-Dev/feeltok

## Technology

- PHP 8.2 without a framework, Apache and MariaDB 10.4, all from XAMPP 8.2.12
- Bootstrap 5 for pages that fit phones and laptops
- SB Admin (Start Bootstrap, MIT licence) for the admin panel

## Run it on your computer

1. Install XAMPP 8.2.12 and start **Apache** and **MySQL** in the XAMPP Control Panel.
2. Put the project in `C:\xampp\htdocs\feeltok`, for example with Git Bash:
   ```
   cd /c/xampp/htdocs
   git clone https://github.com/Akshat-G-Dev/feeltok.git
   ```
3. Open http://localhost/phpmyadmin, choose **Import**, select `database/feeltok.sql` and click **Import**.
4. Open http://localhost/feeltok
5. Open http://localhost/feeltok/tests/tests.php - every line must say **PASS**.

## Test accounts (prototype only)

Every password is `Test@1234`.

| Role | Email |
|---|---|
| Super admin | superadmin@feeltok.test |
| Admin | admin@feeltok.test |
| User | sara@feeltok.test, omar@feeltok.test, lina@feeltok.test, adam@feeltok.test |

## Project structure

```
feeltok/
├── index.php            welcome page
├── register.php         create an account
├── login.php            log in
├── logout.php           log out
├── home.php             home page after log in
├── includes/            config.php, db.php, header.php, footer.php, users.php, validation.php, auth.php
├── assets/              css, images, Bootstrap
├── database/feeltok.sql tables and test accounts
├── tests/tests.php      automatic checks
└── README.md
```

## How we work

- Jira (Scrum) for the backlog and the sprints; Zephyr for the test cases.
- One branch per story (for example `ft-02-welcome-page`). A pull request is reviewed and merged by the other team member.
- Each release is tagged: `v1.0` at the end of Sprint 1, `v2.0` at the end of Sprint 2.

## Team

- Ahmad - GitHub: ahmad-as-dev
- Akshat - GitHub: Akshat-G-Dev
