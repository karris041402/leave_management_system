-- Database: leave_management_db

-- Table for Users (for authentication and role-based access)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL, -- Store hashed password
    role ENUM('admin', 'encoder', 'employee') NOT NULL DEFAULT 'employee',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for Leave Types and their point values
CREATE TABLE IF NOT EXISTS leave_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10) UNIQUE NOT NULL,
    description VARCHAR(255) NOT NULL,
    point_value DECIMAL(5, 3) NOT NULL
);

-- Table for Leave Records (daily entries)
CREATE TABLE IF NOT EXISTS leave_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    leave_date DATE NOT NULL,
    leave_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
);

-- Table for Monthly Leave Summaries
CREATE TABLE IF NOT EXISTS monthly_summaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    month INT NOT NULL,
    year INT NOT NULL,
    vacation_leave_balance DECIMAL(10, 3) NOT NULL,
    sick_leave_balance DECIMAL(10, 3) NOT NULL,
    -- Add other summary fields as needed
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY user_month_year (user_id, month, year),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
