-- ============================================================
-- SKSU Isulan Campus - Graduate-to-Alumni Tracking System
-- Database Schema
-- ============================================================

DROP DATABASE IF EXISTS sksu_alumni;
CREATE DATABASE sksu_alumni CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sksu_alumni;

-- ============================================================
-- USERS TABLE - Centralized authentication for all roles
-- ============================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','registrar','alumni','admin') NOT NULL DEFAULT 'student',
    contact VARCHAR(30),
    address VARCHAR(255),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- STUDENTS TABLE - Graduating students
-- ============================================================
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    student_id VARCHAR(30) UNIQUE NOT NULL,
    course VARCHAR(120) NOT NULL,
    year_level VARCHAR(20) NOT NULL,
    department VARCHAR(120),
    academic_year VARCHAR(20),
    expected_graduation DATE,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- REQUIREMENTS - Uploaded graduation requirements
-- ============================================================
CREATE TABLE requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    requirement_type ENUM('Clearance','Yearbook Form','Graduation Document','Other') NOT NULL,
    title VARCHAR(150) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    remarks TEXT,
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- ============================================================
-- SCHEDULES - Photobooth, graduation, alumni events
-- ============================================================
CREATE TABLE schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    schedule_type ENUM('Photobooth','Graduation','Alumni Event') NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    location VARCHAR(150),
    status ENUM('scheduled','attended','cancelled') DEFAULT 'scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (schedule_type, scheduled_date, scheduled_time)
);

-- ============================================================
-- PAYMENTS - Yearbook fees, graduation fees, donations
-- ============================================================
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    payment_type ENUM('Yearbook Fee','Graduation Fee','Donation','Other') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    reference_no VARCHAR(50) UNIQUE NOT NULL,
    payment_method ENUM('Cash','GCash','Bank Transfer') DEFAULT 'Cash',
    status ENUM('pending','paid','rejected','refunded') DEFAULT 'pending',
    remarks TEXT,
    paid_at DATETIME DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- GRADUATES - Approved graduates with unique Graduate ID
-- ============================================================
CREATE TABLE graduates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    graduate_id VARCHAR(30) UNIQUE NOT NULL,
    course VARCHAR(120),
    department VARCHAR(120),
    academic_year VARCHAR(20),
    graduation_date DATE,
    honors VARCHAR(80),
    approved_by INT,
    approved_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- ALUMNI - Auto-generated from graduates
-- ============================================================
CREATE TABLE alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    graduate_id INT NOT NULL,
    employment_status ENUM('Employed','Unemployed','Self-Employed','Further Studies') DEFAULT 'Unemployed',
    company_name VARCHAR(150),
    job_title VARCHAR(120),
    industry VARCHAR(120),
    work_address VARCHAR(255),
    monthly_income DECIMAL(10,2),
    career_achievements TEXT,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (graduate_id) REFERENCES graduates(id) ON DELETE CASCADE
);

-- ============================================================
-- TRACER REPORTS - Quarterly tracer monitoring
-- ============================================================
CREATE TABLE tracer_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT NOT NULL,
    quarter ENUM('Q1','Q2','Q3','Q4') NOT NULL,
    report_year INT NOT NULL,
    employment_status ENUM('Employed','Unemployed','Self-Employed','Further Studies') NOT NULL,
    company_name VARCHAR(150),
    job_title VARCHAR(120),
    related_to_course ENUM('Yes','No','Partially') DEFAULT 'Yes',
    notes TEXT,
    submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (alumni_id) REFERENCES alumni(id) ON DELETE CASCADE
);

-- ============================================================
-- EVENTS - Alumni events for engagement
-- ============================================================
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(150),
    capacity INT DEFAULT 0,
    created_by INT,
    status ENUM('upcoming','ongoing','completed','cancelled') DEFAULT 'upcoming',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- EVENT REGISTRATIONS - Alumni who joined events
-- ============================================================
CREATE TABLE event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NOT NULL,
    registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_registration (event_id, user_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- ANNOUNCEMENTS - System-wide messages
-- ============================================================
CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    audience ENUM('all','students','alumni','registrar') DEFAULT 'all',
    posted_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (posted_by) REFERENCES users(id) ON DELETE SET NULL
);

-- ============================================================
-- DEFAULT ADMIN + SAMPLE REGISTRAR
-- Password for both: Admin@123 (bcrypt hashed)
-- ============================================================
INSERT INTO users (full_name, email, password, role, contact, address) VALUES
('System Administrator','admin@sksu.edu.ph','$2y$10$wH8QnVqGm5jE7gN0s7y0OeOq3RZVjB9V8tBZJ2j5gE5Vf5qM5p1Yi','admin','09171234567','SKSU Isulan'),
('Registrar Officer','registrar@sksu.edu.ph','$2y$10$wH8QnVqGm5jE7gN0s7y0OeOq3RZVjB9V8tBZJ2j5gE5Vf5qM5p1Yi','registrar','09181234567','SKSU Isulan');

-- Sample event
INSERT INTO events (title, description, event_date, event_time, location, capacity, created_by) VALUES
('Alumni Homecoming 2026','Annual gathering of SKSU Isulan alumni','2026-08-15','14:00:00','SKSU Isulan Gymnasium',500,1);

-- Sample announcement
INSERT INTO announcements (title, body, audience, posted_by) VALUES
('Welcome to the Alumni Tracking System','This platform connects graduates and alumni of SKSU Isulan Campus.','all',1);
