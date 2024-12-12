-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2024 at 11:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rinseclean_lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `username`, `password`, `email`, `phone_number`, `created_at`) VALUES
(1, 'Prudence Kigaru', 'pk@mail.com', 'pk@mail.com', '1111111111', '2024-11-13 11:29:15'),
(2, 'Test staff', 'teststaff@mail.com', 'teststaff@mail.com', '1234567890', '2024-11-13 12:44:52'),
(3, 'Test customer', 'Test customer', 'Testcustomer@mail.com', '333333333', '2024-11-27 06:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `nps_score` int(11) DEFAULT NULL,
  `nps_reason` text DEFAULT NULL,
  `cleanliness_rating` int(11) DEFAULT NULL,
  `timeliness_rating` int(11) DEFAULT NULL,
  `customer_service_rating` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `feedback`
--
DELIMITER $$
CREATE TRIGGER `after_feedback_insert` AFTER INSERT ON `feedback` FOR EACH ROW BEGIN
    -- Update the orders table to set feedback_given to 'yes' when feedback is added
    UPDATE orders 
    SET feedback_given = 'yes'
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_id` varchar(20) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `laundry_type` varchar(50) NOT NULL,
  `fabric_softener` varchar(100) DEFAULT NULL,
  `pickup_time` datetime NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `weight` decimal(5,2) DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `staff_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `feedback_given` enum('yes','no') DEFAULT 'no'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_id`, `customer_name`, `laundry_type`, `fabric_softener`, `pickup_time`, `special_instructions`, `status`, `weight`, `cost`, `payment_status`, `staff_id`, `created_at`, `feedback_given`) VALUES
(1, 'RCLMS00001', 'Test customer', 'Wash & Fold', 'Lavender Scent', '2024-12-12 13:03:00', 'fold neatly', 'Completed', 11.00, 1320.00, 'Confirmed', 2, '2024-12-12 13:03:57', 'no'),
(2, 'RCLMS00002', 'Test customer', 'Ironing Service', 'Clean Cotton', '2024-12-13 13:04:00', 'I need it perfect for my wedding', 'Completed', 10.00, 500.00, 'Confirmed', 2, '2024-12-12 13:04:31', 'no'),
(3, 'RCLMS00003', 'Prudence Kigaru', 'Dry Cleaning', 'Baby Soft', '2024-12-12 13:04:00', 'Fold neatly', 'Completed', 6.00, 600.00, 'Confirmed', 2, '2024-12-12 13:05:07', 'no');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `before_insert_orders` BEFORE INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE order_number INT;
    
    -- Set the order_number to be the next incremented value of id
    SET NEW.order_id = CONCAT('RCLMS', LPAD((SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'orders' AND table_schema = DATABASE()), 5, '0'));
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` int(11) NOT NULL,
  `wash_fold_rate` decimal(10,2) NOT NULL,
  `dry_cleaning_rate` decimal(10,2) NOT NULL,
  `ironing_rate` decimal(10,2) DEFAULT NULL,
  `bedding_rate` decimal(10,2) DEFAULT NULL,
  `stain_removal_rate` decimal(10,2) DEFAULT NULL,
  `specialty_fabric_rate` decimal(10,2) DEFAULT NULL,
  `opening_time` time NOT NULL,
  `closing_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`id`, `wash_fold_rate`, `dry_cleaning_rate`, `ironing_rate`, `bedding_rate`, `stain_removal_rate`, `specialty_fabric_rate`, `opening_time`, `closing_time`, `created_at`, `updated_at`) VALUES
(1, 120.00, 100.00, 50.00, 200.00, 250.00, 300.00, '06:00:00', '18:00:00', '2024-11-14 11:24:59', '2024-11-14 11:27:50');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `reg_number` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active',
  `salary_status` enum('Pending','Paid','Failed') DEFAULT 'Pending',
  `last_paid_date` date DEFAULT NULL,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`reg_number`, `name`, `phone_number`, `username`, `password`, `status`, `salary_status`, `last_paid_date`, `salary`) VALUES
('RCML-EMP-001', 'Moses Mutiso Uhuru', '+254 705 211 27', 'Moses Mutiso Uhuru', '1111111111', 'active', 'Paid', '2024-11-23', 4000.00),
('RCML-EMP-002', 'Cody Gakpo', '223344556677', 'Cody Gakpo', '223344556677', 'active', 'Paid', '2024-11-22', 7400.00),
('RCML-EMP-003', 'mohammad salah', '12345', 'Salah', 'Salah', 'active', 'Pending', NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `reg_number` varchar(12) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone_number`, `role`, `reg_number`, `name`, `address`, `status`, `created_at`) VALUES
(1, 'admin', 'securepassword123', 'admin@example.com', '1234567890', 'admin', 'RCML-EMP-000', 'Admin', NULL, 'active', '2024-11-13 17:00:13'),
(2, 'Moses Mutiso Uhuru', '1111111111', '', '+254 705 211 27', 'staff', 'RCML-EMP-001', 'Moses Mutiso Uhuru', NULL, 'active', '2024-11-13 17:01:04'),
(8, 'Cody Gakpo', '223344556677', 'cody@example.com', '223344556677', 'staff', 'RCML-EMP-002', 'Cody Gakpo', NULL, 'active', '2024-11-20 21:17:14'),
(9, 'Salah', 'Salah', 'shoeluxx001@gmail.com', '12345', 'staff', 'RCML-EMP-003', 'mohammad salah', NULL, 'active', '2024-11-20 21:46:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`reg_number`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `reg_number` (`reg_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
