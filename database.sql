-- Create Database Schema for Clinic Directory

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO roles (name) VALUES ('Admin'), ('Doctor/Clinic'), ('Patient');

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NULL, -- Nullable for Google Logins
    role_id INT NOT NULL,
    google_id VARCHAR(255) NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    avatar_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin Account (password is 'password' hashed with bcrypt)
-- You should change this after first login
INSERT INTO users (email, password, role_id, name) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Super Admin'),
('doctor@nareshdalal.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 'Dr. Naresh Dalal');

-- We can't safely pre-populate clinic_profiles here without a known user_id for Dr. Naresh Dalal
-- but we can ensure the admin account is set. The user will set up their profile using the UI with:
-- Name: Dr. Naresh Dalal Hospital
-- Location: Jhajjar, Haryana
-- Phone: +918199861552

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default keys for Google OAuth
-- Insert default keys for Google OAuth and SMTP
INSERT INTO settings (setting_key, setting_value) VALUES
('google_client_id', ''),
('google_client_secret', ''),
('smtp_host', 'smtp.gmail.com'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_port', '587');

-- Medical Specialties / Categories
CREATE TABLE IF NOT EXISTS specialties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO specialties (name) VALUES
('Ayurveda'), ('Gynecology'), ('Homeopathy'), ('Neurology'), ('Psychology'), ('General Practice'), ('Dentistry');

-- Patient Profiles
CREATE TABLE IF NOT EXISTS patient_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    phone VARCHAR(50) NOT NULL,
    age INT NULL,
    address TEXT NULL,
    preferred_specialty_id INT NULL,
    prakruti_assessment TEXT NULL,
    psychological_assessment TEXT NULL,
    personality_assessment TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (preferred_specialty_id) REFERENCES specialties(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Themes created by Admin
CREATE TABLE IF NOT EXISTS themes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    header_html TEXT NOT NULL,
    footer_html TEXT NOT NULL,
    css_variables TEXT NOT NULL, -- e.g., default colors
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO themes (name, header_html, footer_html, css_variables) VALUES
('Modern Minimal', '<header style="background: var(--primary-color); padding: 30px 0; color: #fff; box-shadow: 0 4px 6px rgba(0,0,0,0.05);"><div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;"><div>{{logo}}<h1 style="margin:0; font-size:2em; font-weight:700; letter-spacing:-0.5px;">{{clinic_name}}</h1></div><nav style="display:flex; gap:15px; font-weight:500;">{{navigation}}</nav></div></header>', '<footer style="background: #111827; color: #f9fafb; padding: 40px 0; margin-top: 60px;"><div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:20px;"><p style="margin:0; opacity:0.8;">&copy; {{year}} {{clinic_name}}. All rights reserved.</p><div style="display:flex; gap:15px;">{{social_links}}</div></div></footer>', ':root { --primary-color: #4F46E5; --text-color: #1F2937; --bg-color: #F3F4F6; }'),
('Elegant Centered', '<header style="background: #fff; padding: 40px 0; text-align:center; border-bottom: 4px solid var(--primary-color);"><div class="container">{{logo}}<h1 style="margin:10px 0; font-size:2.5em; font-weight:800; color:var(--text-color);">{{clinic_name}}</h1><nav style="display:inline-flex; gap:20px; font-weight:600; text-transform:uppercase; font-size:0.9em; margin-top:15px;">{{navigation}}</nav></div></header>', '<footer style="background: var(--primary-color); color: #fff; padding: 30px 0; margin-top: 60px; text-align:center;"><div class="container"><div style="margin-bottom:15px;">{{social_links}}</div><p style="margin:0; font-size:0.9em;">&copy; {{year}} {{clinic_name}}.</p></div></footer>', ':root { --primary-color: #10B981; --text-color: #111827; --bg-color: #ffffff; }');

-- Clinic / Doctor Profiles
CREATE TABLE IF NOT EXISTS clinic_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE, -- e.g., dr-smith
    clinic_name VARCHAR(255) NOT NULL,
    specialty_id INT NULL,
    theme_id INT NULL,
    primary_color VARCHAR(7) DEFAULT '#000000',
    logo_url VARCHAR(255) NULL,
    address TEXT NULL,
    phone VARCHAR(50) NULL,
    whatsapp VARCHAR(50) NULL,
    facebook VARCHAR(255) NULL,
    instagram VARCHAR(255) NULL,
    consultation_fee DECIMAL(10,2) DEFAULT 0.00,
    is_published TINYINT(1) DEFAULT 0,
    has_paid_branding TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id) ON DELETE SET NULL,
    FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Custom Pages for Clinics (Mini-CMS)
CREATE TABLE IF NOT EXISTS clinic_pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) NOT NULL, -- e.g., contact, services
    content LONGTEXT NOT NULL,
    is_home TINYINT(1) DEFAULT 0, -- Indicates if this is the homepage for the clinic
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_clinic_slug (clinic_id, slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clinic Schedules
CREATE TABLE IF NOT EXISTS clinic_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT NOT NULL UNIQUE,
    slot_duration INT DEFAULT 30, -- Duration in minutes
    monday_start TIME NULL, monday_end TIME NULL,
    tuesday_start TIME NULL, tuesday_end TIME NULL,
    wednesday_start TIME NULL, wednesday_end TIME NULL,
    thursday_start TIME NULL, thursday_end TIME NULL,
    friday_start TIME NULL, friday_end TIME NULL,
    saturday_start TIME NULL, saturday_end TIME NULL,
    sunday_start TIME NULL, sunday_end TIME NULL,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointments
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    clinic_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending_payment', 'pending', 'approved', 'rejected', 'completed') DEFAULT 'pending_payment',
    private_notes TEXT NULL,
    soap_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patient_profiles(user_id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointment Chat Messages
CREATE TABLE IF NOT EXISTS appointment_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    sender_id INT NOT NULL, -- references users.id
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Appointment Medical Attachments (Lab reports, past prescriptions, etc.)
CREATE TABLE IF NOT EXISTS appointment_attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    uploader_id INT NOT NULL, -- usually patient
    file_name VARCHAR(255) NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (uploader_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Saved Medicines for Clinic
CREATE TABLE IF NOT EXISTS medicines_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    default_dosage VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews by Patients for Clinics
CREATE TABLE IF NOT EXISTS clinic_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL UNIQUE,
    patient_id INT NOT NULL,
    clinic_id INT NOT NULL,
    rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
    review_text TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews by Clinics for Patients
CREATE TABLE IF NOT EXISTS patient_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL UNIQUE,
    clinic_id INT NOT NULL,
    patient_id INT NOT NULL,
    rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
    review_text TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (clinic_id) REFERENCES clinic_profiles(id) ON DELETE CASCADE,
    FOREIGN KEY (patient_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Prescriptions
CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL UNIQUE,
    medicines_json TEXT NOT NULL, -- JSON array of {name, dosage, notes}
    general_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Billing Transactions
-- Payments Table (Replaces simple billing_transactions)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_type ENUM('appointment', 'profile_upgrade') NOT NULL,
    reference_id INT NULL, -- ID of the appointment or clinic profile
    base_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) NOT NULL,
    platform_fee DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
