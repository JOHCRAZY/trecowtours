-- Create Database
CREATE DATABASE IF NOT EXISTS trecow_tours;
USE trecow_tours;

-- (Optional) Drop existing tables to refresh schema
DROP TABLE IF EXISTS tour_reviews;
DROP TABLE IF EXISTS tour_guides_assignments;
DROP TABLE IF EXISTS tour_guides;
DROP TABLE IF EXISTS tour_activities;
DROP TABLE IF EXISTS activities;
DROP TABLE IF EXISTS package_inclusions;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS customers;
DROP TABLE IF EXISTS contact_info;
DROP TABLE IF EXISTS tours;

-- Tours table with additional status and check constraints
CREATE TABLE tours (
    tour_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    booking_deadline DATE NOT NULL,
    duration_days INT NOT NULL,
    duration_nights INT NOT NULL,
    starting_point VARCHAR(100) NOT NULL,
    single_price DECIMAL(10,2) NOT NULL,
    couple_price DECIMAL(10,2) NOT NULL,
    total_seats INT NOT NULL,
    available_seats INT NOT NULL,
    status ENUM('active', 'cancelled', 'completed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT chk_dates CHECK (start_date < end_date AND booking_deadline < start_date)
);

-- Package inclusions table with a foreign key constraint and cascade delete
CREATE TABLE package_inclusions (
    inclusion_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    inclusion_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
);
CREATE INDEX idx_package_inclusions_tour_id ON package_inclusions(tour_id);

-- Activities table (added an optional activity_date field)
CREATE TABLE activities (
    activity_id INT PRIMARY KEY AUTO_INCREMENT,
    activity_name VARCHAR(100) NOT NULL,
    description TEXT,
    location VARCHAR(100),
    activity_date DATE
);

-- Tour activities mapping table with cascade delete on both keys
CREATE TABLE tour_activities (
    tour_id INT NOT NULL,
    activity_id INT NOT NULL,
    PRIMARY KEY (tour_id, activity_id),
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE
);

-- Customers table with an index on email
CREATE TABLE customers (
    customer_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX idx_customers_email ON customers(email);

-- Bookings table with an added discount column and check constraint for total_amount
CREATE TABLE bookings (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    customer_id INT NOT NULL,
    booking_type ENUM('single', 'couple') NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL,
    discount_applied DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    CONSTRAINT chk_total_amount CHECK (total_amount >= 0)
);
CREATE INDEX idx_bookings_tour_id ON bookings(tour_id);
CREATE INDEX idx_bookings_customer_id ON bookings(customer_id);

-- Enhanced Contact Information table with a contact_type column
CREATE TABLE contact_info (
    contact_id INT PRIMARY KEY AUTO_INCREMENT,
    platform VARCHAR(50) NOT NULL,
    contact_value VARCHAR(100) NOT NULL,
    contact_type ENUM('social', 'phone', 'email') DEFAULT 'social',
    is_active BOOLEAN DEFAULT TRUE
);

-- Tour guides table for storing guide information
CREATE TABLE tour_guides (
    guide_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mapping table to assign guides to tours
CREATE TABLE tour_guides_assignments (
    tour_id INT NOT NULL,
    guide_id INT NOT NULL,
    assignment_date DATE DEFAULT CURRENT_DATE,
    PRIMARY KEY (tour_id, guide_id),
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    FOREIGN KEY (guide_id) REFERENCES tour_guides(guide_id) ON DELETE CASCADE
);

-- Tour reviews table to store customer feedback and ratings
CREATE TABLE tour_reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    customer_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
);


CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT DEFAULT NULL,
    event_name VARCHAR(100) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
    CONSTRAINT chk_event_time CHECK (end_time > start_time)
);


ALTER TABLE tour_reviews ADD CONSTRAINT unique_customer_tour_review UNIQUE(tour_id, customer_id);


CREATE TABLE booking_history (
    history_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    old_status VARCHAR(20),
    new_status VARCHAR(20),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    changed_by VARCHAR(50)
);

CREATE TABLE tour_images (
    image_id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
);


ALTER TABLE tours ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE;

ALTER TABLE tours ADD CONSTRAINT valid_prices CHECK (single_price > 0 AND couple_price > single_price);

ALTER TABLE bookings ADD COLUMN is_deleted BOOLEAN DEFAULT FALSE;

ALTER TABLE bookings ADD CONSTRAINT valid_discount CHECK (discount_applied >= 0 AND discount_applied <= total_amount);

ALTER TABLE tour_reviews ALTER COLUMN rating SET DEFAULT 5;

ALTER TABLE tours COMMENT 'Contains all tour packages offered by Trecow Tours';