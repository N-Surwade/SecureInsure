-- Insurance Policy Management System Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS insurance_db;
USE insurance_db;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Policies table
CREATE TABLE policies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    policy_name VARCHAR(100) NOT NULL,
    type ENUM('Health', 'Life', 'Vehicle', 'Travel') NOT NULL,
    premium DECIMAL(10,2) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    benefits TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Purchases table
CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    policy_id INT NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Expired', 'Cancelled') DEFAULT 'Active',
    payment_method VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE
);

-- Claims table
CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    policy_id INT NOT NULL,
    claim_reason TEXT NOT NULL,
    claim_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    claim_status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    document_path VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE
);

-- Admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Insert sample admin
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password

-- Insert sample policies (12 total - 3 per type) - Fixed SQL syntax
INSERT INTO policies (policy_name, type, premium, duration, benefits, description) VALUES
('Comprehensive Health Insurance', 'Health', 5000.00, '1 Year', 'Hospitalization Outpatient Dental Maternity', 'Full family health coverage with no waiting period'),
('Critical Illness Cover', 'Health', 2500.00, '1 Year', 'Lump sum on diagnosis of 30 illnesses', 'Financial protection for major diseases'),
('Family Floater Plan', 'Health', 7500.00, '1 Year', 'Covers entire family No individual limits', 'Multi-member health insurance plan'),
('Term Life Insurance', 'Life', 10000.00, '10 Years', 'Death benefit up to 1 Cr Accidental death', 'Pure protection term plan'),
('Whole Life Insurance', 'Life', 15000.00, 'Lifetime', 'Guaranteed returns Life cover', 'Savings Protection till age 100'),
('Child Future Plan', 'Life', 5000.00, '15 Years', 'Education funding Maturity benefit', 'Plan for your childs future goals'),
('Comprehensive Car Insurance', 'Vehicle', 8000.00, '1 Year', 'Own damage Third party Zero dep', 'Full car protection package'),
('Two Wheeler Insurance', 'Vehicle', 1500.00, '1 Year', 'Accident Theft Liability cover', 'Bike protection plan'),
('Commercial Vehicle Cover', 'Vehicle', 12000.00, '1 Year', 'Goods in transit Passenger liability', 'For taxis trucks commercial use'),
('International Travel Insurance', 'Travel', 3000.00, '30 Days', 'Medical emergency Trip delay Baggage', 'Global coverage for travelers'),
('Domestic Travel Plan', 'Travel', 1000.00, '7 Days', 'Flight delay Hotel booking Lost baggage', 'India travel protection'),
('Annual Multi Trip Plan', 'Travel', 12000.00, '1 Year', 'Unlimited trips up to 90 days each', 'Frequent travelers annual plan');
