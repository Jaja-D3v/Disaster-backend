-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 11:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `disaster_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangay_contact_info`
--

CREATE TABLE `barangay_contact_info` (
  `id` int(11) NOT NULL,
  `barangay_name` varchar(100) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `landline` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `facebook_page` varchar(255) DEFAULT NULL,
  `captain_name` varchar(100) DEFAULT NULL,
  `secretary_name` varchar(100) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `long` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_male` int(11) DEFAULT 0,
  `total_female` int(11) DEFAULT 0,
  `total_families` int(11) DEFAULT 0,
  `total_male_senior` int(11) DEFAULT 0,
  `total_female_senior` int(11) DEFAULT 0,
  `total_0_4_years` int(11) DEFAULT 0,
  `source` varchar(255) DEFAULT NULL,
  `date_added` date DEFAULT curdate(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_contact_info`
--

INSERT INTO `barangay_contact_info` (`id`, `barangay_name`, `contact_number`, `landline`, `email`, `facebook_page`, `captain_name`, `secretary_name`, `lat`, `long`, `created_at`, `total_male`, `total_female`, `total_families`, `total_male_senior`, `total_female_senior`, `total_0_4_years`, `source`, `date_added`, `added_by`) VALUES
(1, 'Updated Barangay', '09171234567', '1234567', 'testuser@gmail.com', 'https://facebook.com/updatedbarangay', 'Juan Dela Cruz', 'Maria Santos', 14.5995000, 120.98420000, '2025-10-29 10:38:53', 1500, 1600, 800, 200, 250, 300, 'Barangay census 2025', '2025-10-29', NULL),
(2, 'Barangay Mabini', '09181234567', '045-234-5678', 'mabini.brgy@gmail.com', 'https://facebook.com/brgymabini', 'Jose Ramirez', 'Ana Dizon', 15.4823450, 120.96321000, '2025-10-29 10:38:53', 0, 0, 0, 0, 0, 0, NULL, '2025-10-29', NULL),
(3, 'Barangay Maligaya', '09281234567', '045-345-6789', 'maligaya.barangay@gmail.com', 'https://facebook.com/brgymaligaya', 'Pedro Reyes', 'Liza Cruz', 15.4753210, 120.95123400, '2025-10-29 10:38:53', 0, 0, 0, 0, 0, 0, NULL, '2025-10-29', NULL),
(4, 'Barangay San Roque', '09391234567', '045-456-7890', 'sanroque.barangay@gmail.com', 'https://facebook.com/brgysanroque', 'Ricardo Gomez', 'Ella Mendoza', 15.4802100, 120.95987600, '2025-10-29 10:38:53', 0, 0, 0, 0, 0, 0, NULL, '2025-10-29', NULL),
(5, 'Barangay Sto. Niño', '09491234567', '045-567-8901', 'stonino.barangay@gmail.com', 'https://facebook.com/brgystonino', 'Carlos Bautista', 'Joyce Lim', 15.4738900, 120.96214500, '2025-10-29 10:38:53', 0, 0, 0, 0, 0, 0, NULL, '2025-10-29', NULL),
(15, 'Barangay Test', '09123456789', '1234567', 'test@barangay.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:45:45', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(16, 'Barangay Test', '09123456789', '1234567', 'test@gmail.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:49:52', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(17, 'Barangay Test', '09123456789', '1234567', 'test@askjda.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:52:07', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(18, 'Barangay Test', '09123456789', '1234567', 'phonetest@smart.com.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:14', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(19, 'Barangay Test', '09123456789', '1234567', 'testuser@gmail.com', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:34', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(20, 'Barangay Test', '09123456789', '1234567', 'testuser@deped.gov.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:51', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(21, 'Barangay Test', '09123456789', '1234567', 'student@ust.edu.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:04:16', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(22, 'Barangay Test', '09234563789', '1234567', 'teacher@ust.edu.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:06:43', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12);

-- --------------------------------------------------------

--
-- Table structure for table `community_notice`
--

CREATE TABLE `community_notice` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disaster_mapping`
--

CREATE TABLE `disaster_mapping` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disaster_update`
--

CREATE TABLE `disaster_update` (
  `id` int(11) NOT NULL,
  `img_path` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `date_time` datetime NOT NULL,
  `disaster_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `added_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `payment_intent_id` varchar(255) NOT NULL,
  `donor_name` varchar(255) DEFAULT 'Anonymous Donor',
  `donor_email` varchar(255) DEFAULT 'noemail@disasterready.app',
  `amount` int(11) NOT NULL,
  `currency` varchar(10) DEFAULT 'PHP',
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `payment_intent_id`, `donor_name`, `donor_email`, `amount`, `currency`, `payment_method`, `status`, `created_at`) VALUES
(1, 'pi_wt1Bx6BrPV8zvfvPpGFHtApq', 'Jared Abrera', 'jared@example.com', 2000, 'PHP', 'gcash', 'paid', '2025-10-29 13:09:39'),
(2, 'pi_3vL6uNaxiMpNRwRgK76aMaQu', 'Jared Abrera', 'jared@example.com', 2000, 'PHP', NULL, 'pending', '2025-10-29 13:15:45');

-- --------------------------------------------------------

--
-- Table structure for table `evacuation_center`
--

CREATE TABLE `evacuation_center` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 0,
  `current_evacuees` int(11) NOT NULL DEFAULT 0,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `lat` decimal(10,8) DEFAULT NULL,
  `long` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incident_reports`
--

CREATE TABLE `incident_reports` (
  `id` int(11) NOT NULL,
  `reporter_name` varchar(100) DEFAULT NULL,
  `reporter_contact` varchar(20) NOT NULL,
  `description` text NOT NULL,
  `media` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Ongoing','Resolved') DEFAULT 'Pending',
  `responded_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lat` decimal(10,8) DEFAULT NULL,
  `lng` decimal(11,8) DEFAULT NULL,
  `severity` enum('Critical','Moderate','Minor') DEFAULT 'Minor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_reports`
--

INSERT INTO `incident_reports` (`id`, `reporter_name`, `reporter_contact`, `description`, `media`, `status`, `responded_by`, `created_at`, `updated_at`, `lat`, `lng`, `severity`) VALUES
(1, 'dsfsbdfsdfj', '639943910539', 'sdjfsdfsbdjfdsbfsjdf', 'asdbasjdmas', 'Ongoing', 'Jared Abrera', '2025-10-29 10:15:05', '2025-10-29 10:21:18', NULL, NULL, 'Minor');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_history`
--

CREATE TABLE `password_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_request_logs`
--

CREATE TABLE `password_request_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_registrations`
--

CREATE TABLE `pending_registrations` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `code` varchar(6) DEFAULT NULL,
  `expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `road_advisories`
--

CREATE TABLE `road_advisories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `date_time` datetime NOT NULL,
  `status` varchar(50) NOT NULL,
  `added_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 2,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `barangay` varchar(255) NOT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `status`, `barangay`, `last_logged_in`, `created_at`, `updated_at`, `login_attempts`, `last_attempt_at`) VALUES
(12, 'jared', 'jaredabrera48@gmail.com', '$2y$10$cCyVld4UxxLVfI798Ngts.YWpcPt7ag7DtsutRqqBIbqn7vAbeYDy', 1, 'active', 'Central', NULL, '2025-10-29 10:28:40', '2025-10-29 10:30:46', 0, NULL),
(13, 'Neo', 'jaredabrera44@gmail.com', '$2y$10$D/GqW7q4W4bREw2cuNSQG.Y2yHwNWc7WmKwSLUcvrHS1EdV05F8CC', 2, 'active', 'Central', NULL, '2025-10-29 10:30:34', '2025-10-29 10:30:34', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `weather_advisories`
--

CREATE TABLE `weather_advisories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `date_time` datetime NOT NULL,
  `added_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_contact_info`
--
ALTER TABLE `barangay_contact_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_added_by` (`added_by`);

--
-- Indexes for table `community_notice`
--
ALTER TABLE `community_notice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disaster_mapping`
--
ALTER TABLE `disaster_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_created_by` (`created_by`);

--
-- Indexes for table `disaster_update`
--
ALTER TABLE `disaster_update`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evacuation_center`
--
ALTER TABLE `evacuation_center`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `incident_reports`
--
ALTER TABLE `incident_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_history`
--
ALTER TABLE `password_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_request_logs`
--
ALTER TABLE `password_request_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `road_advisories`
--
ALTER TABLE `road_advisories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `weather_advisories`
--
ALTER TABLE `weather_advisories`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangay_contact_info`
--
ALTER TABLE `barangay_contact_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `community_notice`
--
ALTER TABLE `community_notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `disaster_mapping`
--
ALTER TABLE `disaster_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `disaster_update`
--
ALTER TABLE `disaster_update`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `evacuation_center`
--
ALTER TABLE `evacuation_center`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `incident_reports`
--
ALTER TABLE `incident_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `password_request_logs`
--
ALTER TABLE `password_request_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `road_advisories`
--
ALTER TABLE `road_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `weather_advisories`
--
ALTER TABLE `weather_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barangay_contact_info`
--
ALTER TABLE `barangay_contact_info`
  ADD CONSTRAINT `fk_added_by` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `disaster_mapping`
--
ALTER TABLE `disaster_mapping`
  ADD CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `password_history`
--
ALTER TABLE `password_history`
  ADD CONSTRAINT `password_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



CREATE TABLE archived_users (
    id INT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    role INT,
    status VARCHAR(50),
    barangay VARCHAR(255),
    last_logged_in DATETIME,
    created_at DATETIME,
    updated_at DATETIME,
    login_attempts INT,
    last_attempt_at DATETIME,
    archived_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

