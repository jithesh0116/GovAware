-- Smart Government Scheme Awareness Portal Database Schema (Strict English Version)
-- Compatible with MySQL/MariaDB (XAMPP Setup)

CREATE DATABASE IF NOT EXISTS `smart_gov_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smart_gov_db`;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `age` INT NOT NULL,
    `gender` ENUM('Male', 'Female', 'Other') NOT NULL,
    `state` VARCHAR(100) NOT NULL,
    `occupation` VARCHAR(100) NOT NULL,
    `annual_income` DECIMAL(12, 2) NOT NULL,
    `education` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `role` VARCHAR(50) DEFAULT 'SuperAdmin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Schemes Table (Simplified English Only)
CREATE TABLE IF NOT EXISTS `schemes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) NOT NULL DEFAULT 'All', -- 'All' for central, or specific state names
    `description` TEXT NOT NULL,
    `benefits` TEXT NOT NULL,
    `min_age` INT DEFAULT 0,
    `max_age` INT DEFAULT 120,
    `gender_eligibility` ENUM('All', 'Male', 'Female', 'Other') DEFAULT 'All',
    `max_income` DECIMAL(12, 2) DEFAULT 99999999.00,
    `allowed_occupations` TEXT, -- Comma separated or 'All'
    `allowed_education` TEXT, -- Comma separated or 'All'
    `docs_required` TEXT NOT NULL,
    `deadline` DATE DEFAULT NULL,
    `official_link` VARCHAR(255) DEFAULT '#',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Saved/Bookmarked Schemes Table
CREATE TABLE IF NOT EXISTS `saved_schemes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `scheme_id` INT NOT NULL,
    `saved_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_user_scheme` (`user_id`, `scheme_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`scheme_id`) REFERENCES `schemes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Feedback Table
CREATE TABLE IF NOT EXISTS `feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `subject` VARCHAR(150) NOT NULL,
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Notifications Table (Simplified English Only)
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `scheme_id` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`scheme_id`) REFERENCES `schemes`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ========================================================
-- INSERTING SEED DATA
-- ========================================================

-- Admins: Seed standard administrator account
-- Username: admin
-- Password: AdminPassword123 (hashed: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
INSERT INTO `admins` (`username`, `password`, `email`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@smartgov.gov.in', 'SuperAdmin');

-- Schemes: Populate with initial English Schemes
INSERT INTO `schemes` 
(`name`, `category`, `state`, `description`, `benefits`, `min_age`, `max_age`, `gender_eligibility`, `max_income`, `allowed_occupations`, `allowed_education`, `docs_required`, `deadline`, `official_link`) 
VALUES
(
    'PM Kisan Samman Nidhi', 
    'Agriculture', 
    'All',
    'An initiative by the government of India that provides up to Rs 6,000 per year in three equal installments to small and marginal farmers.', 
    'Direct benefit transfer of Rs 6,000 per annum, paid in three equal installments of Rs 2,000 every 4 months directly to the bank account.', 
    18, 100, 'All', 300000.00, 'Farmer,Agriculture', 'All', 
    'Land ownership documents (Khata/Patta), Aadhaar Card, Bank Account Passbook, Passport size photograph', 
    '2026-12-31', 
    'https://pmkisan.gov.in/'
),
(
    'Pradhan Mantri Awas Yojana (Gramin)', 
    'Housing', 
    'All',
    'A social welfare program created by the Indian Government, to provide pucca houses with basic amenities to all homeless householders and those households living in dilapidated houses.', 
    'Financial assistance of Rs. 1.2 Lakhs in plains and Rs. 1.3 Lakhs in hilly/difficult areas for house construction.', 
    18, 70, 'All', 200000.00, 'Farmer,Laborer,Unemployed,Self-Employed,Other', 'All', 
    'Aadhaar card, Bank account details, Consent document to use Aadhaar, Swachh Bharat Mission registration number, Job Card number', 
    '2026-11-30', 
    'https://pmayg.nic.in/'
),
(
    'Post-Matric Scholarship Scheme', 
    'Education', 
    'Karnataka',
    'Financial assistance provided to students belonging to minority communities or economically weaker sections to pursue post-matric or post-secondary courses.', 
    'Full reimbursement of tuition fees and a monthly maintenance allowance ranging from Rs. 300 to Rs. 1200 depending on the course.', 
    15, 30, 'All', 250000.00, 'Student', '10th Pass,12th Pass,Diploma,Graduate,Post-Graduate', 
    'Income Certificate, Caste Certificate, Marksheets of previous class, Fee receipt of current year, Bank Passbook, Aadhaar card', 
    '2026-10-15', 
    'https://ssp.postmatric.karnataka.gov.in/'
),
(
    'Ayushman Bharat Pradhan Mantri Jan Arogya Yojana', 
    'Healthcare', 
    'All',
    'The largest health assurance scheme in the world which aims to provide free access to health insurance coverage for low-income earners in the country.', 
    'Provides a health cover of Rs. 5 Lakhs per family per year for secondary and tertiary care hospitalization across public and private empaneled hospitals.', 
    0, 120, 'All', 150000.00, 'Farmer,Laborer,Unemployed,Self-Employed,Other', 'All', 
    'Aadhaar card/Ration card, Identity proof, Family structure proof, Active mobile number', 
    NULL, 
    'https://pmjay.gov.in/'
),
(
    'Karnataka Gruha Lakshmi Scheme', 
    'Social Welfare', 
    'Karnataka',
    'A welfare program launched by the Government of Karnataka, offering financial aid directly to the woman head of a household.', 
    'Direct cash transfer of Rs. 2,000 every month directly to the bank account of the designated female head of the family.', 
    18, 90, 'Female', 99999999.00, 'Farmer,Laborer,Unemployed,Self-Employed,Other,Housewife', 'All', 
    'Aadhaar card of self and husband, Mobile number linked with Aadhaar, Bank Account statement/Passbook copy, Ration card (BPL/AAY/APL)', 
    '2026-12-31', 
    'https://sevasindhu.karnataka.gov.in/'
),
(
    'Stand-Up India Scheme', 
    'Self-Employment', 
    'All',
    'A government initiative to promote entrepreneurship among women and Scheduled Castes (SC) or Scheduled Tribes (ST).', 
    'Loans between Rs. 10 Lakhs and Rs. 1 Crore to at least one SC or ST borrower and at least one woman borrower per bank branch for setting up a greenfield enterprise.', 
    18, 70, 'All', 99999999.00, 'Self-Employed,Business Owner,Other', '10th Pass,12th Pass,Diploma,Graduate,Post-Graduate', 
    'Aadhaar Card, Proof of identity and address, Business Plan/Project Report, Caste Certificate (SC/ST if applicable), Assets and Liabilities statement, Bank statement for last 6 months', 
    '2026-09-30', 
    'https://www.standupmitra.in/'
),
(
    'Karnataka Yuva Nidhi Scheme', 
    'Education', 
    'Karnataka',
    'An unemployment assistance program of the Government of Karnataka which offers monthly allowance for unemployed graduates and diploma holders of the state.', 
    'Monthly allowance of Rs. 3,000 for graduates and Rs. 1,500 for diploma holders for a period of up to 2 years or until they get a job.', 
    20, 30, 'All', 500000.00, 'Unemployed,Student', 'Diploma,Graduate,Post-Graduate', 
    'Degree/Diploma Passing Certificate, Marks Cards, Aadhaar Card, Domicile certificate of Karnataka, Bank Passbook, Unemployed Declaration self-certificate', 
    '2026-12-31', 
    'https://sevasindhugs.karnataka.gov.in/'
);

-- Notifications: Initial entries
INSERT INTO `notifications` (`title`, `content`, `scheme_id`) VALUES
(
    'Application open for Yuva Nidhi 2026', 
    'Eligible graduates and diploma holders from academic year 2024-25 and 2025-26 can now register on Seva Sindhu portal.', 
    7
),
(
    'PM Kisan 17th Installment Date Announced', 
    'The 17th installment of PM Kisan Nidhi will be credited directly to farmers bank accounts next month. Please ensure e-KYC is complete.', 
    1
);
