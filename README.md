# GovAware-Government Scheme Awareness Portal

GovAware is a responsive, full-stack PHP and MySQL web application designed to help citizens discover, explore, and identify government welfare schemes offered by central and state governments.

Developed as an MCA final-year project, the system provides a centralized platform with scheme discovery, eligibility checking, scheme comparison, citizen dashboards, and administrative management.

## 🌟 Key Features

### 1. Citizen Features

- **Scheme Discovery:** Browse government schemes through a structured catalogue.
- **Smart Eligibility Checker:** A multi-step eligibility wizard that evaluates citizens based on age, gender, state, annual household income, occupation, and education.
- **Interactive Search:** Dynamic autocomplete search with database-driven suggestions.
- **Scheme Comparison:** Compare up to three selected schemes side by side.
- **Citizen Dashboard:** View profile information and manage saved or bookmarked schemes.
- **Scheme Details:** Access detailed information about individual schemes.
- **PDF Reports:** Generate print-friendly scheme reports using the browser's print-to-PDF functionality.
- **Feedback System:** Submit feedback and contact messages through the portal.

### 2. Administrator Features

- **Admin Dashboard:** View statistics for schemes, registered citizens, feedback messages, and notifications.
- **Scheme Management:** Add, view, edit, and delete scheme records.
- **Citizen Management:** View registered citizen profiles.
- **Feedback Management:** Review submitted citizen feedback.
- **Notification Management:** Publish and manage scheme announcements and updates.

### 3. Interface and Usability

- Responsive layout for desktop, tablet, and mobile devices.
- English-language interface.
- Light and dark theme support.
- Interactive elements and smooth hover effects.
- Dynamic search and AJAX-based interactions.
- Consistent layouts and reusable interface components.

## 🧰 Technology Stack

| Component | Technology |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP |
| Database | MySQL |
| Database Connectivity | PHP PDO |
| Asynchronous Operations | AJAX, Fetch API |
| Local Development | XAMPP |
| Database Administration | phpMyAdmin |
| Theme Persistence | Browser LocalStorage |
| Comparison State | Browser SessionStorage |

## 📂 Project Directory Structure

```text
smart-gov-portal/
│
├── index.php
├── about.php
├── login.php
├── register.php
├── logout.php
├── schemes.php
├── scheme-detail.php
├── eligibility.php
├── dashboard.php
├── contact.php
│
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── schemes.php
│   ├── users.php
│   ├── feedback.php
│   └── notifications.php
│
├── includes/
│   ├── db.php
│   ├── auth.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── api/
│   ├── schemes.php
│   ├── search.php
│   ├── eligibility.php
│   └── save_scheme.php
│
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   │
│   └── js/
│       ├── main.js
│       ├── dark-mode.js
│       ├── search.js
│       ├── comparison.js
│       └── eligibility.js
│
├── database/
│   └── schema.sql
│
└── README.md
```

## ⚙️ Installation and Setup

Follow these steps to run GovAware locally using XAMPP.

### Step 1: Install XAMPP

1. Download and install XAMPP.
2. Open the XAMPP installation directory.
3. Navigate to:

   `C:\xampp\htdocs\`

4. Place the project folder inside `htdocs`.

The project directory should be:

```text
C:\xampp\htdocs\smart-gov-portal\
```

### Step 2: Start Apache and MySQL

1. Open the XAMPP Control Panel.
2. Start **Apache**.
3. Start **MySQL**.
4. Ensure both services are running.

### Step 3: Create the Database

1. Open your browser and navigate to:

   `http://localhost/phpmyadmin/`

2. Select **New**.
3. Create a database named:

   `smart_gov_db`

4. Select the collation:

   `utf8mb4_unicode_ci`

### Step 4: Import the Database

1. Select the newly created `smart_gov_db` database.
2. Open the **Import** tab.
3. Choose the project database file:

   `database/schema.sql`

4. Click **Import** or **Go**.
5. Verify that the database tables have been created successfully.

The expected tables are:

- `users`
- `admins`
- `schemes`
- `saved_schemes`
- `feedback`
- `notifications`

### Step 5: Configure the Database Connection

Check the database connection settings in:

```text
includes/db.php
```

For a default local XAMPP installation, the configuration may use:

```php
$host = '127.0.0.1';
$db   = 'smart_gov_db';
$user = 'root';
$pass = '';
```

Update these values if your MySQL configuration is different.

### Step 6: Run GovAware

Open your browser and visit:

**http://localhost/smart-gov-portal/**

The GovAware homepage should now be accessible.

## 🔐 Administrator Access

Administrator login page:

`http://localhost/smart-gov-portal/admin/login.php`

Use the administrator account configured in the database.

If demo credentials are provided with your local database, use those credentials for testing. Change any default password before deployment or public use.

## 🛡️ Security Features

The application incorporates several security practices:

- **SQL Injection Protection:** Uses PDO prepared statements and parameter binding for database operations.
- **XSS Mitigation:** Uses HTML output escaping through PHP's `htmlspecialchars()` function.
- **Password Hashing:** Uses PHP's `password_hash()` function with bcrypt for password storage.
- **Session-Based Authentication:** Uses PHP sessions to maintain authenticated user access.
- **Administrator Access Checks:** Uses administrator session validation to restrict access to administrative pages.

These measures help reduce common web application security risks. Production deployment should also include CSRF protection, secure session configuration, input validation, and appropriate database permissions.

## 🎯 Project Objectives

- Improve awareness of central and state government welfare schemes.
- Make scheme information easier for citizens to access.
- Simplify the process of checking scheme eligibility.
- Help citizens compare available schemes.
- Provide administrators with tools to manage scheme information.
- Develop a centralized and user-friendly government scheme information portal.

## 🚀 Future Enhancements

- Integration with verified government scheme information sources.
- Multilingual interface support.
- Email and SMS notifications.
- More advanced eligibility recommendation features.
- Additional accessibility improvements.

## 🎓 Project Information

**Project Name:** GovAware  
**Project Type:** MCA Final-Year Project  
**Project Category:** Full-Stack Web Application  
**Domain:** Government Welfare Scheme Awareness

---

*GovAware – Making Government Schemes Easier to Discover and Understand.*
