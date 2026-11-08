# Smart Government Scheme Awareness Portal (GovAware)

A complete responsive full-stack PHP-MySQL citizen empowerment web application designed to bridge the awareness gap between deserving citizens and central/state government welfare initiatives.

Designed as an MCA final year full-stack project utilizing modern aesthetics, English-only layout specifications, eligibility engine calculation, comparison matrices, and administrator CRUD panels.

## 🌟 Key Features

1. **Strict English Interface**: Simple, streamlined user interfaces in all directories, dropping dual field fallbacks to keep columns compact and clean.
2. **Smart Eligibility Wizard**: A dynamic 3-step citizen eligibility checker analyzing Age, Gender, State, Annual Household Income, Occupation, and Education to calculate and display eligible schemes in real-time.
3. **Interactive Autocomplete Search**: Debounced search matching database values dynamically as the user types.
4. **Side-by-Side Scheme Comparison**: Citizen utility allowing comparison of up to 3 chosen schemes in a comparison grid.
5. **Interactive Citizen Dashboard**: Personalized profiles display saved/bookmarked schemes with quick dynamic AJAX removal.
6. **Form PDF Download Engine**: Custom print styling sheets formatting scheme details beautifully as an official PDF report using standard browser options (`window.print()`).
7. **Secure Administrator Control Panel**:
   * Statistics panel tracking total schemes, citizens, feedback messages, and alerts.
   * Full CRUD Operations for managing schemes inside standard fields.
   * Citizen profile registry management.
   * Interactive Feedback view dashboard.
   * Scheme announcements broadcaster module linking updates directly to scheme records.
8. **Modern Aesthetic System**: Vibrant color palettes, smooth hover states, micro-interactions, dark mode toggle variables, and fully responsive media queries.

---

## 📂 Project Directory Structure

```
smart-gov-portal/
├── index.php                  # Home page (Hero + Autocomplete + News Ticker)
├── about.php                  # Portal mission, interactive timelines, FAQs
├── login.php                  # Citizen sign-in page with route checking
├── register.php               # Citizen sign-up page (Age, Income, Occupation, etc.)
├── logout.php                 # Destroys active user sessions
├── schemes.php                # Schemes catalog directory with advanced filters
├── scheme-detail.php          # Detailed details, dynamic bookmarking, PDF prints
├── eligibility.php            # Dynamic multi-step eligibility calculation page
├── dashboard.php              # Saved bookmarks & citizen profile demographics
├── contact.php                # Feedbacks & Contact coordinates submission form
├── admin/
│   ├── index.php              # Administrative Dashboard panel index
│   ├── login.php              # Secure officer authenticate page
│   ├── schemes.php            # Manage schemes (Add / Edit / Delete CRUD forms)
│   ├── users.php              # Citizens profile registry viewer
│   ├── feedback.php           # Inbox reading citizen feedback messages
│   └── notifications.php     # Publishing and deleting scheme alerts & bulletins
├── includes/
│   ├── db.php                 # PDO MySQL secure database connection configuration
│   ├── auth.php               # Roles authorization and route-guards checks
│   ├── functions.php          # Common helper variables (Sanitization, Badges, Rupees)
│   ├── header.php             # Common header navigation, alerts, Dark toggler
│   └── footer.php             # Common footer, sticky comparison bar, modals
├── api/
│   ├── schemes.php            # AJAX list catalog query endpoint (compare fetcher)
│   ├── search.php             # Autocomplete keyword search suggestions debouncer
│   ├── eligibility.php        # Dynamic matches search algorithm
│   └── save_scheme.php        # Toggle bookmark registry actions
├── assets/
│   ├── css/
│   │   ├── style.css          # Main responsive design HSL variables & typography
│   │   └── admin.css          # Dark themed admin layout panels & tables styling
│   └── js/
│       ├── main.js            # General initialization & loading spinners
│       ├── dark-mode.js       # LocalStorage theme attributes persist controllers
│       ├── search.js          # Debounced fetch search autocomplete handler
│       ├── comparison.js      # SessionStorage compared schemes builder
│       └── eligibility.js     # Wizard transitions and card listings compiler
├── database/
│   └── schema.sql             # SQL database schema and seeds datasets
└── README.md                  # Comprehensive configuration manual
```

---

## 🛠️ Step-by-Step Installation Guide (XAMPP Environment)

Follow these steps to deploy and run this project locally:

### Step 1: Clone or Copy Project Files
1. Ensure **XAMPP** is installed on your computer.
2. Navigate to your XAMPP installation directory (typically `C:\xampp\htdocs\`).
3. Create a subdirectory named `smart-gov-portal` and copy all these project files into it.
   * File path should look like: `C:\xampp\htdocs\smart-gov-portal\index.php`

### Step 2: Start XAMPP Control Panel
1. Open the **XAMPP Control Panel** application.
2. Click **Start** on the **Apache** module.
3. Click **Start** on the **MySQL** module.
4. Ensure both modules are running and highlighted in green.

### Step 3: Set Up and Import the Database
1. Open your web browser and go to: `http://localhost/phpmyadmin/`
2. Click on **New** in the left sidebar to create a new database.
3. Name the database **`smart_gov_db`** and select **Collation** as `utf8mb4_unicode_ci` or `utf8mb4_general_ci`. Click **Create**.
4. Select the newly created `smart_gov_db` database, and go to the **Import** tab on the top menu.
5. Click **Choose File** and browse to select the **`schema.sql`** file located inside the project:
   * Directory: `C:\xampp\htdocs\smart-gov-portal\database\schema.sql`
6. Click **Import** (or **Go**) at the bottom.
7. Verification: You should see 6 tables successfully created (`users`, `admins`, `schemes`, `saved_schemes`, `feedback`, `notifications`) populated with realistic sample data.

### Step 4: Run the Portal
1. Open your browser and navigate to: **`http://localhost/smart-gov-portal/`**
2. The homepage should load instantly with clean styling and active data listings.

---

## 🔑 Default Credentials

### 1. Administrative Panel Login
* **URL**: `http://localhost/smart-gov-portal/admin/login.php`
* **Username**: `admin`
* **Password**: `AdminPassword123`

### 2. Sample Citizens / Users Registration
* You can register new citizen profiles directly using the **Register** form.
* Alternatively, log in with an existing sample user if created, or proceed to build custom profiles dynamically.

---

## 🛡️ Security Measures Implemented

* **SQL Injection Prevention**: All database queries are executed using **PHP Data Objects (PDO)** with prepared statements and parameter bindings.
* **XSS Attack Prevention**: Input data is sanitized using a custom `sanitize()` function applying `htmlspecialchars()` prior to being output in HTML.
* **Password Hashing**: Citizen passwords are encrypted using strong **Bcrypt (`password_hash()`)** algorithms during registration.
* **Administrative Session Guard**: Admin panel routes (`admin/`) checks and validates `$_SESSION['is_admin']` to prevent unauthorized access.
