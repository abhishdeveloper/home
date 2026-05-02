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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin Account (password is 'password' hashed with bcrypt)
-- You should change this after first login
INSERT INTO users (email, password, role_id, name) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'Super Admin');

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default keys for Google OAuth
INSERT INTO settings (setting_key, setting_value) VALUES
('google_client_id', ''),
('google_client_secret', '');

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
    age INT NULL,
    address TEXT NULL,
    preferred_specialty_id INT NULL,
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
('Modern Blue', '<header class="modern-header"><div class="container"><h1>{{clinic_name}}</h1><nav>{{navigation}}</nav></div></header>', '<footer class="modern-footer"><div class="container"><p>&copy; {{year}} {{clinic_name}}. All rights reserved.</p><div>{{social_links}}</div></div></footer>', ':root { --primary-color: #007bff; --text-color: #333; }'),
('Classic Green', '<header class="classic-header"><div class="container" style="text-align:center;"><h1>{{clinic_name}}</h1><hr><nav>{{navigation}}</nav></div></header>', '<footer class="classic-footer"><div class="container" style="text-align:center;"><p>&copy; {{year}} {{clinic_name}}.</p><div>{{social_links}}</div></div></footer>', ':root { --primary-color: #28a745; --text-color: #222; }');

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
    is_published TINYINT(1) DEFAULT 0,
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
