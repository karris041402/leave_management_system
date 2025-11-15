-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 02:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leave_management_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `leave_absence_equivalents`
--

CREATE TABLE `leave_absence_equivalents` (
  `id` int(11) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `leave_earned` decimal(5,3) NOT NULL,
  `absences_w_o_pay` decimal(5,1) NOT NULL,
  `leave_earned_2` decimal(5,3) NOT NULL,
  `absences_w_o_pay_2` decimal(5,1) NOT NULL,
  `leave_earned_3` decimal(5,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_absence_equivalents`
--

INSERT INTO `leave_absence_equivalents` (`id`, `no_of_days`, `leave_earned`, `absences_w_o_pay`, `leave_earned_2`, `absences_w_o_pay_2`, `leave_earned_3`) VALUES
(1, 1, 0.042, 0.5, 1.229, 15.5, 0.604),
(2, 2, 0.083, 1.0, 1.208, 16.0, 0.583),
(3, 3, 0.125, 1.5, 1.188, 16.5, 0.562),
(4, 4, 0.167, 2.0, 1.167, 17.0, 0.542),
(5, 5, 0.208, 2.5, 1.146, 17.5, 0.521),
(6, 6, 0.250, 3.0, 1.125, 18.0, 0.500),
(7, 7, 0.292, 3.5, 1.104, 18.5, 0.479),
(8, 8, 0.333, 4.0, 1.083, 19.0, 0.458),
(9, 9, 0.375, 4.5, 1.063, 19.5, 0.437),
(10, 10, 0.417, 5.0, 1.042, 20.0, 0.417),
(11, 11, 0.458, 5.5, 1.021, 20.5, 0.396),
(12, 12, 0.500, 6.0, 1.000, 21.0, 0.375),
(13, 13, 0.542, 6.5, 0.979, 21.5, 0.354),
(14, 14, 0.583, 7.0, 0.958, 22.0, 0.333),
(15, 15, 0.625, 7.5, 0.938, 22.5, 0.312),
(16, 16, 0.667, 8.0, 0.917, 23.0, 0.292),
(17, 17, 0.708, 8.5, 0.854, 23.5, 0.271),
(18, 18, 0.750, 9.0, 0.833, 24.0, 0.250),
(19, 19, 0.792, 9.5, 0.875, 24.5, 0.229),
(20, 20, 0.833, 10.0, 0.833, 25.0, 0.208),
(21, 21, 0.875, 10.5, 0.813, 25.5, 0.187),
(22, 22, 0.917, 11.0, 0.792, 26.0, 0.167),
(23, 23, 0.958, 11.5, 0.771, 26.5, 0.146),
(24, 24, 1.000, 12.0, 0.750, 27.0, 0.125),
(25, 25, 1.042, 12.5, 0.729, 27.5, 0.104),
(26, 26, 1.083, 13.0, 0.708, 28.0, 0.083),
(27, 27, 1.125, 13.5, 0.687, 28.5, 0.062),
(28, 28, 1.167, 14.0, 0.667, 29.0, 0.042),
(29, 29, 1.208, 14.5, 0.646, 29.5, 0.021),
(30, 30, 1.250, 15.0, 0.625, 0.0, 0.000);

-- --------------------------------------------------------

--
-- Table structure for table `leave_earned_by_day`
--

CREATE TABLE `leave_earned_by_day` (
  `id` int(11) NOT NULL,
  `no_of_days` int(11) NOT NULL,
  `leave_earned` decimal(5,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_earned_by_day`
--

INSERT INTO `leave_earned_by_day` (`id`, `no_of_days`, `leave_earned`) VALUES
(1, 1, 0.042),
(2, 2, 0.083),
(3, 3, 0.125),
(4, 4, 0.167),
(5, 5, 0.208),
(6, 6, 0.250),
(7, 7, 0.292),
(8, 8, 0.333),
(9, 9, 0.375),
(10, 10, 0.417),
(11, 11, 0.458),
(12, 12, 0.500),
(13, 13, 0.542),
(14, 14, 0.583),
(15, 15, 0.625),
(16, 16, 0.667),
(17, 17, 0.708),
(18, 18, 0.750);

-- --------------------------------------------------------

--
-- Table structure for table `leave_records`
--

CREATE TABLE `leave_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `leave_date` date NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` varchar(255) NOT NULL,
  `point_value` decimal(5,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `code`, `description`, `point_value`) VALUES
(1, 'SL', 'Sick Leave', 1.000),
(2, 'VL', 'Vacation Leave', 1.000),
(3, 'SPL', 'Special Leave', 1.000),
(4, 'HD', 'Half Day', 0.500),
(5, 'ABS', 'Absent', 1.000),
(6, 'VL8', 'Vacation Leave (8 hours)', 1.000),
(7, 'VL10', 'Vacation Leave (10 hours)', 1.250),
(8, 'VHD', 'Vacation Half Day', 0.500),
(9, 'SL8', 'Sick Leave (8 hours)', 1.000),
(10, 'SL10', 'Sick Leave (10 hours)', 1.250),
(11, 'SPL1', 'Special Leave 1', 1.000),
(12, 'SPL2', 'Special Leave 2', 1.000),
(13, 'SPL3', 'Special Leave 3', 1.000),
(14, 'CTO', 'Compensatory Time Off', 1.000),
(15, 'M/FL8', 'Maternity/Paternity/Family Leave (8 hours)', 1.000),
(16, 'M/FL10', 'Maternity/Paternity/Family Leave (10 hours)', 1.250),
(17, 'SHD', 'Special Half Day', 0.500),
(18, 'AB8', 'Absence (8 hours)', 1.000),
(19, 'AB10', 'Absence (10 hours)', 1.250);

-- --------------------------------------------------------

--
-- Table structure for table `monthly_summaries`
--

CREATE TABLE `monthly_summaries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `vacation_leave_balance` decimal(10,3) NOT NULL,
  `sick_leave_balance` decimal(10,3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_equivalents`
--

CREATE TABLE `time_equivalents` (
  `id` int(11) NOT NULL,
  `hour` int(11) DEFAULT NULL,
  `hour_equivalent_day` decimal(5,3) DEFAULT NULL,
  `minute` int(11) DEFAULT NULL,
  `minute_equivalent_day` decimal(5,3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `time_equivalents`
--

INSERT INTO `time_equivalents` (`id`, `hour`, `hour_equivalent_day`, `minute`, `minute_equivalent_day`) VALUES
(1, 1, 0.125, 1, 0.002),
(2, 2, 0.250, 2, 0.004),
(3, 3, 0.375, 3, 0.006),
(4, 4, 0.500, 4, 0.008),
(5, 5, 0.625, 5, 0.010),
(6, 6, 0.750, 6, 0.012),
(7, 7, 0.875, 7, 0.015),
(8, 8, 1.000, 8, 0.017),
(9, NULL, NULL, 9, 0.019),
(10, NULL, NULL, 10, 0.021),
(11, NULL, NULL, 11, 0.023),
(12, NULL, NULL, 12, 0.025),
(13, NULL, NULL, 13, 0.027),
(14, NULL, NULL, 14, 0.029),
(15, NULL, NULL, 15, 0.031),
(16, NULL, NULL, 16, 0.033),
(17, NULL, NULL, 17, 0.035),
(18, NULL, NULL, 18, 0.037),
(19, NULL, NULL, 19, 0.040),
(20, NULL, NULL, 20, 0.042),
(21, NULL, NULL, 21, 0.044),
(22, NULL, NULL, 22, 0.046),
(23, NULL, NULL, 23, 0.048),
(24, NULL, NULL, 24, 0.050),
(25, NULL, NULL, 25, 0.052),
(26, NULL, NULL, 26, 0.054),
(27, NULL, NULL, 27, 0.056),
(28, NULL, NULL, 28, 0.058),
(29, NULL, NULL, 29, 0.060),
(30, NULL, NULL, 30, 0.062),
(31, NULL, NULL, 31, 0.065),
(32, NULL, NULL, 32, 0.067),
(33, NULL, NULL, 33, 0.069),
(34, NULL, NULL, 34, 0.071),
(35, NULL, NULL, 35, 0.073),
(36, NULL, NULL, 36, 0.075),
(37, NULL, NULL, 37, 0.077),
(38, NULL, NULL, 38, 0.079),
(39, NULL, NULL, 39, 0.081),
(40, NULL, NULL, 40, 0.083),
(41, NULL, NULL, 41, 0.085),
(42, NULL, NULL, 42, 0.087),
(43, NULL, NULL, 43, 0.090),
(44, NULL, NULL, 44, 0.092),
(45, NULL, NULL, 45, 0.094),
(46, NULL, NULL, 46, 0.096),
(47, NULL, NULL, 47, 0.098),
(48, NULL, NULL, 48, 0.100),
(49, NULL, NULL, 49, 0.102),
(50, NULL, NULL, 50, 0.104),
(51, NULL, NULL, 51, 0.106),
(52, NULL, NULL, 52, 0.108),
(53, NULL, NULL, 53, 0.110),
(54, NULL, NULL, 54, 0.112),
(55, NULL, NULL, 55, 0.115),
(56, NULL, NULL, 56, 0.117),
(57, NULL, NULL, 57, 0.119),
(58, NULL, NULL, 58, 0.121),
(59, NULL, NULL, 59, 0.123),
(60, NULL, NULL, 60, 0.125);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','encoder','employee') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_name`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'System Administrator', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-11-15 01:31:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leave_absence_equivalents`
--
ALTER TABLE `leave_absence_equivalents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_of_days` (`no_of_days`);

--
-- Indexes for table `leave_earned_by_day`
--
ALTER TABLE `leave_earned_by_day`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_of_days` (`no_of_days`);

--
-- Indexes for table `leave_records`
--
ALTER TABLE `leave_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_month_year` (`user_id`,`month`,`year`);

--
-- Indexes for table `time_equivalents`
--
ALTER TABLE `time_equivalents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_hour` (`hour`),
  ADD UNIQUE KEY `unique_minute` (`minute`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leave_absence_equivalents`
--
ALTER TABLE `leave_absence_equivalents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `leave_earned_by_day`
--
ALTER TABLE `leave_earned_by_day`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `leave_records`
--
ALTER TABLE `leave_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=248;

--
-- AUTO_INCREMENT for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_equivalents`
--
ALTER TABLE `time_equivalents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leave_records`
--
ALTER TABLE `leave_records`
  ADD CONSTRAINT `leave_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `leave_records_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`);

--
-- Constraints for table `monthly_summaries`
--
ALTER TABLE `monthly_summaries`
  ADD CONSTRAINT `monthly_summaries_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
