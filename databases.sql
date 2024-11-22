CREATE TABLE IF NOT EXISTS customers (
    id INT NOT NULL AUTO_INCREMENT,          
    username VARCHAR(50) NOT NULL UNIQUE,    
    password VARCHAR(255) NOT NULL,          
    email VARCHAR(100) NOT NULL UNIQUE,      
    phone_number VARCHAR(15) NOT NULL UNIQUE, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS orders (
    id INT NOT NULL AUTO_INCREMENT,               
    order_id VARCHAR(20),                        
    customer_name VARCHAR(100) NOT NULL,         
    laundry_type VARCHAR(50) NOT NULL,          
    fabric_softener VARCHAR(100),                
    pickup_time DATETIME NOT NULL,               
    special_instructions TEXT,                   
    status VARCHAR(20) DEFAULT 'Pending',        
    weight DECIMAL(5, 2),                        -- Direct weight input (in kilograms)
    cost DECIMAL(10, 2) NOT NULL,  
    payment_status VARCHAR(20) NOT NULL DEFAULT 'Pending', -- Ensure payment status is included
    staff_id INT, -- Foreign key to reference the staff responsible for the order
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- Trigger to generate custom order ID with prefix 'RCLMS'
DELIMITER //

CREATE TRIGGER before_insert_orders
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    SET NEW.order_id = CONCAT('RCLMS', LPAD(NEW.id, 5, '0')); 
END //

DELIMITER ;

CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL UNIQUE,
    role ENUM('admin', 'staff') DEFAULT 'staff',  -- Default role set to 'staff'
    reg_number VARCHAR(12) UNIQUE,  -- Registration number for staff (added)
    name VARCHAR(100) NOT NULL,  -- Name of the staff (added)
    address VARCHAR(255),  -- Address of the staff (added)
    status VARCHAR(20) DEFAULT 'active',  -- Status of the staff (added)
    payout DECIMAL(10,2) DEFAULT 0.00,  -- Payout for staff (added)
    pay_rate DECIMAL(10,2) DEFAULT 0.00,  -- Pay rate for staff (added)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (username, password, email, phone_number, role)
VALUES ('admin', 'securepassword123', 'admin@example.com', '1234567890', 'admin');


CREATE TABLE rates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    wash_fold_rate DECIMAL(10, 2) NOT NULL,
    dry_cleaning_rate DECIMAL(10, 2) NOT NULL,
    ironing_rate DECIMAL(10, 2),
    bedding_rate DECIMAL(10, 2),
    stain_removal_rate DECIMAL(10, 2),
    specialty_fabric_rate DECIMAL(10, 2),
    opening_time TIME NOT NULL,
    closing_time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO rates (wash_fold_rate, ironing_rate, bedding_rate, dry_cleaning_rate, stain_removal_rate, specialty_fabric_rate, opening_time, closing_time) 
VALUES 
(120.00, 50.00, 200.00, 100.00, 250.00, 300.00, '06:00 AM', '06:00 PM');


CREATE TABLE staff (
    reg_number VARCHAR(50) PRIMARY KEY,          -- Use reg_number as the primary key, e.g., "RCLMS XXXX"
    name VARCHAR(100) NOT NULL,                  -- Staff member's full name
    phone_number VARCHAR(15) NOT NULL,           -- Staff phone number
    username VARCHAR(50) NOT NULL UNIQUE,        -- Username for login, unique per staff
    password VARCHAR(255) NOT NULL,              -- Password for login
    status VARCHAR(20) DEFAULT 'active',         -- Status of the staff, with default value 'active'
    base_salary DECIMAL(10, 2) DEFAULT 0.00,     -- Staff base salary
    bonuses DECIMAL(10, 2) DEFAULT 0.00,         -- Staff bonuses
    deductions DECIMAL(10, 2) DEFAULT 0.00,      -- Staff deductions
    salary_status ENUM('Pending', 'Paid', 'Failed') DEFAULT 'Pending',  -- Track salary payment status
    last_paid_date DATE                          -- Date of last salary payment
);



CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_id INT,  -- Link to the order
    score INT,
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE leaves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT,
    leave_type ENUM('paid', 'unpaid'),
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);

CREATE TABLE salary_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT,
    salary_amount DECIMAL(10, 2),
    payout_date DATE,
    status ENUM('paid', 'pending'),
    FOREIGN KEY (staff_id) REFERENCES staff(staff_id)
);

-- Create the clock_in_out table with correct foreign key reference
CREATE TABLE clock_in_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reg_number VARCHAR(50) NOT NULL,  -- Reference to reg_number
    clock_in DATETIME,
    clock_out DATETIME,
    FOREIGN KEY (reg_number) REFERENCES staff(reg_number) ON DELETE CASCADE  -- Correct foreign key reference
);