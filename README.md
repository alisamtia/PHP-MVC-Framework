# PHP MVC Framework

> A lightweight, extensible MVC framework for building modern PHP applications — featuring routing, middleware, authentication, authorization, validation, database abstraction, sessions, CSRF protection, file uploads, email, rate limiting, and more.

**Built from scratch with PHP, PDO, Composer, and a service-oriented architecture.**

## ⚡ Overview

**PHP MVC Framework** is a custom-built PHP web framework designed to provide the core architecture and security features needed to build structured, maintainable web applications without relying on a full-stack framework.

It follows the **Model–View–Controller (MVC)** pattern with a clean separation between HTTP handling, application logic, data access, and presentation.

### Highlights

* 🚀 RESTful routing with dynamic parameters and resource routes
* 🔐 Session-based authentication and role-based authorization
* 🛡️ Middleware pipeline with CSRF protection
* ✅ Reusable request validation
* 💾 PDO database abstraction with prepared statements
* 📧 SMTP email support through PHPMailer
* 📁 File upload service
* 🚦 IP/email-based rate limiting
* 🔑 Password reset system
* 🧩 Service-oriented architecture
* 🗂️ Session and flash-data management
* 🎨 PHP view rendering
* 📦 Composer & PSR-4 autoloading
* 📡 JSON request handling
* ⚠️ Structured HTTP error handling

## 🏗️ Architecture

```text
Request
   │
   ▼
 Router
   │
   ▼
Middleware
   │
   ▼
Controller
   │
   ├──► Service
   │      │
   │      ▼
   │    Model
   │      │
   │      ▼
   │   Database
   │
   ▼
 View
   │
   ▼
Response
```

The architecture separates responsibilities so applications remain easier to maintain, test, and extend.

## 🛣️ Routing

Supports common HTTP methods, dynamic parameters, regex constraints, optional parameters, resource routes, and method spoofing.

```php
$router->get('/', [IndexController::class, 'index']);

$router->post('/login', [AuthController::class, 'login']);

$router->get('/users/{id:\d+}', [UserController::class, 'show']);

$router->delete('/users/{id:\d+}', [UserController::class, 'destroy']);
```

**Supported:** `GET` · `POST` · `PUT` · `PATCH` · `DELETE`

## 🔐 Authentication & Authorization

* User registration and login/logout
* Bcrypt password hashing
* Remember-me authentication
* Expiring login tokens
* Session regeneration
* Role-based authorization
* Secure cookie configuration
* User lookup and profile management
* Avatar uploads

## 🛡️ Middleware

Protect routes using reusable middleware:

```php
$router->get('/dashboard', [
    DashboardController::class,
    'index'
])->middleware('auth');
```

Built-in middleware:

```text
auth
authenticated
guest
admin
manager
```

Multiple middleware can be chained together, and custom middleware can be created for application-specific requirements.

## 🧱 CSRF Protection

State-changing requests are automatically protected against **Cross-Site Request Forgery (CSRF)**.

```text
POST · PUT · PATCH · DELETE
```

## ✅ Validation

A reusable validation layer supporting:

* Required fields
* String validation
* Minimum/maximum length
* Numeric validation
* Minimum/maximum values
* Array validation
* Array item validation
* `in` validation
* Input trimming
* Custom error messages
* Reusable validator classes

```php
$validator->validate($request->all(), [
    'username' => ['required', 'string', 'min:3'],
    'email'    => ['required', 'email'],
]);
```

## 💾 Database

PDO-based database abstraction using parameterized queries.

```php
$db->query(
    "SELECT * FROM users WHERE email = ?",
    [$email]
);
```

Available operations:

```text
query()
fetch()
fetchAll()
fetchColumn()
```

The database layer uses prepared statements to help protect against SQL injection.

## 🧩 Service Architecture

Business logic can be separated into dedicated services instead of placing everything inside controllers.

```text
App/
├── Controllers/
├── Models/
├── Services/
├── Validators/
└── Middlewares/
```

This keeps controllers focused and makes larger applications easier to organize.

## 📧 Email

PHPMailer-based SMTP email service supporting:

* HTML and plain-text emails
* SMTP configuration
* Custom senders
* Named recipients
* Exception-based error handling

## 📁 File Uploads

Dedicated file upload service providing:

* Unique filenames
* Organized upload directories
* Multiple upload destinations
* Error handling

```text
uploads/
├── avatars/
└── listings/
```

## 🚦 Rate Limiting

Built-in throttling and attempt tracking for:

* Login attempts
* Password reset requests
* Email-based limits
* IP-based limits
* Custom identifiers

Supports configurable attempt limits, time windows, cooldowns, and automatic resets.

## 💬 Session Management

A dedicated session abstraction:

```php
Session::start();

Session::set('user_id', $userId);
Session::get('user_id');

Session::has('user_id');

Session::flash('success', 'Profile updated.');

Session::pull('success');

Session::unset('user_id');

Session::all();
Session::destroy();
```

Includes flash messages, temporary data, input preservation, and secure session cleanup.

## 🎨 View System

PHP-based view rendering with:

* Data extraction
* Shared view data
* Flash messages
* Old form input
* Error notifications
* Success notifications

```php
return View::render('users/profile', [
    'user' => $user
]);
```

## 🔄 HTTP Responses

Provides helpers for redirects and HTTP responses:

```php
return redirect('/dashboard');
```

Supports redirects, previous-URL handling, flash data, status codes, and custom HTTP error pages.

## 🔑 Password Reset

Complete password recovery flow:

```text
Request Reset
      ↓
Generate Token
      ↓
Send Email
      ↓
Verify Token
      ↓
Validate Password
      ↓
Update Password
```

Includes secure random tokens, expiration, email notifications, validation, and secure password updates.

## 🌐 Request Handling

Supports:

* GET parameters
* POST data
* JSON bodies
* URL-encoded data
* PUT/PATCH/DELETE bodies
* HTTP method spoofing

```php
$request->all();
$request->tAll();
$request->has('email');
$request->except('password');
$request->method();
$request->isGet();
$request->isPost();
```

## 📦 Installation

```bash
git clone https://github.com/alisamtia/PHP-MVC-Framework.git
cd PHP-MVC-Framework

composer install
npm install
```

Configure your application and database settings, then point your web server's document root to:

```text
Public/
```

## 📂 Project Structure

```text
PHP-MVC-Framework/
│
├── App/
│   ├── Controllers/
│   ├── Middlewares/
│   ├── Models/
│   ├── Services/
│   └── Validators/
│
├── Core/
│   ├── App.php
│   ├── Auth.php
│   ├── Database.php
│   ├── LimitService.php
│   ├── Middleware.php
│   ├── RedirectResponse.php
│   ├── Request.php
│   ├── Router.php
│   ├── Session.php
│   ├── Validator.php
│   └── View.php
│
├── Public/
├── views/
├── config.php
├── functions.php
├── routes.php
├── composer.json
└── README.md
```

## 🔒 Security

Security is a core consideration of the framework.

* Prepared SQL statements
* Bcrypt password hashing
* CSRF protection
* Session regeneration
* Secure cookie configuration
* Authentication & authorization middleware
* Rate limiting
* Expiring authentication tokens
* Password reset tokens
* Structured HTTP error handling

> **Note:** This project is primarily intended for learning, experimentation, and small-to-medium PHP applications. Perform your own security review before using it in production.

## 🎯 Philosophy

The goal isn't to recreate Laravel.

The goal is to understand **how a framework actually works under the hood**.

Instead of hiding routing, authentication, middleware, validation, sessions, and database operations behind abstractions, this project builds those systems from the ground up.

## 📜 License

MIT License

## 👨‍💻 Author

**Ali Samtia**

This framework was **designed and built from scratch by me**.

AI was used to help **analyze and summarize the project's features for this README**.

Built with **PHP, PDO, Composer, and a service-oriented architecture**.

⭐ If you find the project interesting, consider starring the repository.
