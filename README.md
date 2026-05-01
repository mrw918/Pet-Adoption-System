# Pet Adoption Platform

## Project Overview
Pet Adoption Platform is a full-stack dynamic web application for browsing pets, submitting adoption applications, and managing records through an admin dashboard.

## Tech Stack
- Frontend: HTML, CSS, JavaScript
- Backend: PHP (mysqli)
- Database: MySQL

## Core Features
- User authentication: register, login, logout, password reset
- Pet browsing with search/filter and interactive UI
- Adoption application flow (user side)
- Admin dashboard with pet CRUD and application review
- Audit/log pages for admin operations

## Live Demo
- URL: [https://petadopt-dt2.infinityfreeapp.com](https://petadopt-dt2.infinityfreeapp.com)
- Test account (user): `user1 / 123456`
- Test account (admin): `mrw / 123456`

## Local Setup (XAMPP)
1. Copy this project folder into XAMPP `htdocs`.
2. Create a local MySQL database and import `sql/database.sql`.
3. Update DB credentials in:
   - `dbconnect.php`
   - `include/dbconnect.php`
4. Start Apache and MySQL in XAMPP.
5. Open `http://localhost/WT_DT2_Group2/index.php`.

## Deployment Setup (InfinityFree)
1. Create a hosting account and MySQL database on InfinityFree.
2. Import `sql/database.sql` via phpMyAdmin.
3. Upload project files to hosting `htdocs`.
4. Update DB credentials in both:
   - `dbconnect.php`
   - `include/dbconnect.php`
5. Open the live URL and verify login, browse, apply, and admin pages.

## Database Notes
- Database script: `sql/database.sql`
- Uses relational tables for users, pets, and adoption applications.
- Uses prepared statements (`mysqli_prepare`) for SQL safety.

## Submission Checklist
- Zipped project folder
- Contribution log
- Peer evaluation form
- 1-2 page project PDF (purpose, features, challenges, screenshots)
- GitHub repository link with meaningful commit history
- Live hosted URL
- In-class demo ready

## Team
- Member 1: [Ma Ruowen]
- Member 2: [Wu Siyi]
- Member 3: [Shi Jiarui]
