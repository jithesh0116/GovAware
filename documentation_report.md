# Project Documentation Report: Smart Government Scheme Awareness Portal (GovAware)

---

## 1. Introduction

The **Smart Government Scheme Awareness Portal (GovAware)** is a comprehensive full-stack web application designed to bridge the persistent information gap between the government's welfare schemes and eligible citizens. In developing countries like India, the state and central governments launch dozens of social assistance programs in sectors like agriculture, education, healthcare, housing, and social welfare. However, the benefits of these schemes often fail to reach targeted segments of the population because citizens are unaware of their eligibility or are overwhelmed by bureaucratic requirements.

GovAware solves this problem by providing a centralized, simplified, and highly interactive digital solution. It enables citizens to:
* Easily discover government schemes by categories and states.
* Instantly verify their eligibility through a smart, multi-step checker tool.
* Save and track their favorite schemes.
* Generate and download offline information reports as print-ready PDFs.
* Submit feedbacks or seek assistance.

For portal administrators, the system provides a robust control dashboard with full CRUD capabilities, citizen registry overviews, feedback inboxes, and real-time alert broadcasts. By consolidating information and providing dynamic eligibility calculations, the portal lowers entry barriers and ensures a more equitable distribution of public welfare.

---

## 2. Objectives

The primary objective of the portal is to simplify public welfare discovery through modern technology. Specifically, the system aims to achieve the following:

* **Centralize Welfare Information**: Provide a single, well-organized English catalog of active state and central government programs.
* **Automate Eligibility Evaluation**: Minimize citizens' confusion by calculating which schemes they qualify for using custom multi-step parameters (Age, Gender, State, Income, Occupation, and Education).
* **Enable Offline Access**: Deliver easily printable/downloadable scheme details so rural or offline citizens can prepare paperwork before applying.
* **Empower Administrators (Officers)**: Facilitate instant CRUD adjustments to schemes, registry oversight, and announcement broadcasts to maintain accurate portal records.
* **Encourage Civic Interaction**: Offer structured feedback mechanisms so public inquiries reach administrative dashboards securely.

---

## 3. Functional Requirements

Functional requirements describe the specific services, modules, and workflows that the system must provide.

### 3.1 Public Citizen Modules
* **Hero Search & Autocomplete**: A front-page keyword search box that queries the database via debounced AJAX fetch API, showing matching suggestions instantly.
* **Welfare Catalog**: Filterable card-based display of schemes grouped by states, age range, income, and occupations.
* **Smart Eligibility Wizard**: A dynamic, 3-step demographics calculator that takes user parameters, calculates matching logic in an API, and updates matched schemes instantly without page reloads.
* **Scheme Comparison Matrix**: Allows citizens to select up to three different schemes and view their criteria (benefits, required documents, deadlines) side-by-side.
* **PDF Exporter**: A clean `@media print` style sheet that allows citizens to download detailed scheme reports as print-ready PDF files.
* **Contact & Feedback Forms**: Sanitized forms permitting citizens to write queries that save straight to administrative inboxes.

### 3.2 Citizen Account Modules
* **Demographics Profile**: Custom user registration requiring verified state, age, gender, occupation, annual income, and education coordinates.
* **Bookmarks Manager**: Dynamic AJAX toggle buttons saving or removing bookmarked schemes directly within user accounts.
* **Personalized Dashboard**: Visual citizen profile displaying demographics metrics and saved bookmark grids.

### 3.3 Officer/Admin Control Modules
* **Sliding-Tab Logins**: Unified role selector pre-selecting Officer authentication when accessed from administrative routes.
* **Analytics Dashboard**: Stats-driven cards reporting scheme totals, citizen registries, feedback logs, and active alert systems.
* **Schemes CRUD Panel**: Complete management suite permitting administrative additions, modifications, or deletions of government schemes.
* **Citizens Registry**: Searchable read-only grid presenting details of registered accounts.
* **Broadcaster Bulletin**: Live announcement system permitting officers to broadcast urgent updates linked directly to welfare records.

---

## 4. Non-Functional Requirements

Non-functional requirements specify the operational criteria, constraints, and quality metrics of the web portal.

| Quality Attribute | Specification Details & Implementation Measures |
|:---|:---|
| **Security & Privacy** | • Protect citizen credentials in the `users` and `admins` tables using **Bcrypt (`password_hash()`)** hashing.<br>• Secure all SQL inputs using **PDO Prepared Statements** to eliminate SQL Injection vulnerabilities.<br>• Prevent Cross-Site Scripting (XSS) using specialized HTML sanitization routines on outputs. |
| **Performance** | • Fast dynamic search responses under **300ms** utilizing client-side debounced queries to prevent database request choking.<br>• Clean AJAX payload responses under **100ms** by compiling JSON objects directly at server-side endpoints. |
| **Reliability** | • Standard **PDO Exception Handlers** reporting descriptive connection states if database interruptions occur.<br>• Graceful fallback layouts in user dashboard profiles if search returns empty datasets. |
| **Usability & Design** | • Premium responsive user interface matching desktop and mobile viewports utilizing clean vanilla CSS grids.<br>• Intuitive visual navigation containing active nav link indicators and card loading spinners.<br>• Seamless light/dark theme persistence utilizing browser `localStorage` hooks. |
| **Maintainability** | • Clean, modular file organization separating templates (`includes/`), API routes (`api/`), administrative resources (`admin/`), and layout stylesheets (`assets/`). |

---

## 5. Conclusion

The **Smart Government Scheme Awareness Portal (GovAware)** is a robust, full-stack digital solution aimed at maximizing the impact of social welfare schemes. By substituting bureaucratic complexity with a simplified 3-step eligibility engine, side-by-side comparison tables, and print-ready PDF downloads, it empowers citizens to claim benefits they deserve. Supported by an advanced, dark-themed administrative dashboard, officers are equipped with modern CRUD modules to maintain portal records efficiently. The resulting system is clean, secure, performance-optimized, and highly responsive—making it an exemplary demonstration of modern full-stack web engineering suitable for an MCA final year project.
