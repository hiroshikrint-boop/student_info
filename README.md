# StudentInfo PCNL
**Philippine College of Northwestern Luzon** — Student Information System

A simple student records system for my Web Application Development final project.
Students register and manage their own profile, and the admin (registrar) manages all student records.

- **Live site:** https://[firstname-lastname].infinityfreeapp.com
- **GitHub:** https://github.com/[your-username]/student-info
- **Student:** [Firstname Lastname]

## Features
- Student registration and login (passwords hashed with `password_hash()`)
- Two roles: **admin** and **student**
- **Admin:** add, view, edit and delete students (CRUD), search, and filter by course
- **Student:** view own record and update own email and address
- Adding a student creates the login account and the student record together (database transaction)
- Student number availability check while registering (JavaScript fetch / AJAX)
- Live search on the students list
- Validation on the client (JavaScript / HTML5) and on the server (PHP)
- Black / blue / white premium UI (Bootstrap 5 + custom theme), responsive
- CSRF tokens on every form, including delete
- Flash messages, admin dashboard stats, student ID-card profile

## Technologies
HTML5, CSS3, Bootstrap 5, JavaScript, PHP, MySQL

## Security
- Prepared statements (PDO) to stop SQL injection
- `htmlspecialchars()` on output to stop XSS
- Passwords hashed, session ID regenerated at login
- Admin pages check the role in PHP, and a student can only see and edit their own record

## Test accounts

| Role    | Username | Password |
|---------|----------|----------|
| Admin   | admin    | admin123 |
| Student | juan     | user123  |
| Student | maria    | user123  |

## How to set up
1. Create a MySQL database (phpMyAdmin).
2. Import `database/schema.sql`.
3. Open `config.php` and type your database host, name, username and password.
4. Upload all files to the `htdocs` folder of your hosting (or XAMPP `htdocs` for local testing).
5. Open the site and login.

## Database
- `users` (id, username, password, role)
- `courses` (id, code, name)
- `students` (id, user_id, student_no, full_name, email, course_id, year_level, address)

Each student belongs to one user account (one-to-one) and one course (many students per course).
Deleting a user also deletes the student record (ON DELETE CASCADE).

## Files
- `config.php` - database connection and helper functions
- `header.php` / `footer.php` - shared layout
- `register.php`, `login.php`, `logout.php` - authentication
- `students.php`, `student_form.php`, `student_delete.php` - admin student CRUD
- `profile.php` - student profile
- `check_student.php` - used by JavaScript to check the student number
- `_student_fields.php` - shared form fields (register + admin form)
- `script.js`, `style.css` - JavaScript and CSS
