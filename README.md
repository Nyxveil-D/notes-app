# Laravel Notes App

A Laravel MVP for creating and managing personal notes. Features user registration, secure authentication, ownership-based note authorization, and automated tests.

## Project Overview

Laravel Notes App is a minimal viable product that enables users to register, log in, and manage their personal notes. The application demonstrates modern Laravel practices including authentication, authorization, validation, and automated testing.

## Features

- **User Authentication**: Registration and login with secure password hashing and session management
- **Notes CRUD**: Create, read, update, and delete personal notes
- **Note Ownership Authorization**: Users can only access and modify their own notes
- **Input Validation**: Server-side validation for user registration, login, and note data
- **Login Rate Limiting**: Protection against brute-force attacks (5 failed attempts per email + IP address)
- **MySQL Database**: Persistent storage with proper foreign key constraints and cascade delete
- **Automated Tests**: Feature tests covering authentication, note CRUD, validation, authorization, and security

## Tech Stack

- **Framework**: Laravel 13.25
- **Language**: PHP 8.3+
- **Database**: MySQL 
- **Templating**: Blade
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint

## Requirements

- PHP 8.3 or higher
- Composer
- MySQL
- Node.js & npm (for frontend assets)

## Installation

1. **Clone or extract the project**:
   ```bash
   cd notes-app
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install JavaScript dependencies**:
   ```bash
   npm install
   ```

4. **Generate application key** (if not already set):
   ```bash
   php artisan key:generate
   ```

5. **Build frontend assets**:
   ```bash
   npm run build
   ```

## Environment Configuration

1. **Copy environment file**:
   ```bash
   cp .env.example .env
   ```

2. **Configure database** in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=notes_app
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

3. **Session storage** (database-backed sessions):
   ```
   SESSION_DRIVER=database
   ```

4. **Cache store** (database-backed cache):
   ```
   CACHE_STORE=database
   ```

## Database Setup

1. **Create the development database**:
   ```bash
   mysql -u root -p -e "CREATE DATABASE notes_app;"
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate
   ```

   This will create:
   - `users` table
   - `notes` table with foreign key to users (cascade delete on user removal)
   - `sessions` table
   - Cache and job tables

## Testing Setup

### Development Database
- Database: `notes_app`
- Configured in `.env`

### Testing Database
- Database: `notes_app_testing`
- Must be created manually before running tests
- Configured in `phpunit.xml`
- Laravel's `RefreshDatabase` trait resets and prepares the test database for each test run

### Run Tests

```bash
php artisan test
```

Tests cover authentication, note CRUD operations, validation, authorization, and security behavior.

## Running the Application

### Development Server

Start the local development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

### Watch Frontend Assets

In another terminal, rebuild assets on changes:
```bash
npm run dev
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── AuthenticatedSessionController.php   (Login/logout)
│   │   │   └── RegisteredUserController.php         (Registration)
│   │   └── NoteController.php                       (Notes CRUD)
│   ├── Requests/
│   │   ├── LoginRequest.php
│   │   ├── RegisterRequest.php
│   │   ├── StoreNoteRequest.php
│   │   └── UpdateNoteRequest.php
│   ├── Policies/
│   │   └── NotePolicy.php                           (Authorization)
│   └── Models/
│       ├── User.php
│       └── Note.php
database/
├── migrations/                                      (Schema)
├── factories/                                       (Test data)
└── seeders/
tests/
├── Feature/
│   ├── AuthenticationFeatureTest.php
│   └── NoteFeatureTest.php
└── Unit/
```

## Security & Authorization

- **Authentication**: Session-based using Laravel's built-in authentication system
- **Authorization**: Users can only access their own notes (enforced in `NotePolicy` and `NoteController`)
- **Password Hashing**: Passwords are hashed using Laravel's password cast
- **Login Rate Limiting**: 5 failed attempts allowed per email + IP address before temporary lockout
- **CSRF Protection**: Automatically enforced on all form submissions
- **Input Validation**: All user input is validated server-side
- **Cascade Delete**: When a user is deleted, all their notes are automatically removed

## Database Schema

### users
```
id (primary key)
name
email (unique)
email_verified_at
password (hashed)
remember_token
timestamps
```

### notes
```
id (primary key)
user_id (foreign key → users, cascade on delete)
title
content
timestamps
```

## License

This project is open-source software licensed under the MIT license.
