-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2025 at 02:29 AM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_contact_info`
--


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

--
-- Dumping data for table `community_notice`
--

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

--
-- Dumping data for table `disaster_mapping`
--

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

--
-- Dumping data for table `disaster_update`
--

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

--
-- Dumping data for table `evacuation_center`
--

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

--
-- Dumping data for table `password_history`
--

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

--
-- Dumping data for table `road_advisories`
--

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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

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
-- Dumping data for table `weather_advisories`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangay_contact_info`
--
ALTER TABLE `barangay_contact_info`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `evacuation_center`
--
ALTER TABLE `evacuation_center`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `evacuation_center`
--
ALTER TABLE `evacuation_center`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
-- AUTO_INCREMENT for table `road_advisories`
--
ALTER TABLE `road_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `weather_advisories`
--
ALTER TABLE `weather_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

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


CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(255) NOT NULL,
  expires_at DATETIME NOT NULL
);


CREATE TABLE password_request_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE incident_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_name VARCHAR(100) NULL,
    reporter_contact VARCHAR(20) NOT NULL,
    description TEXT NOT NULL,
    media VARCHAR(255) NULL,      -- store the uploaded photo path as string
    status ENUM('Pending','Ongoing','Resolved') DEFAULT 'Pending',
    responded_by VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE incident_reports ADD COLUMN lat DECIMAL(10,8) NULL, ADD COLUMN lng DECIMAL(11,8) NULL, ADD COLUMN severity ENUM('Critical', 'Moderate', 'Minor') DEFAULT 'Minor';



CREATE TABLE pending_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(100),
    password VARCHAR(255),
    barangay VARCHAR(50),
    code VARCHAR(6),
    expires DATETIME
);



CREATE TABLE donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_intent_id VARCHAR(255) NOT NULL,
    donor_name VARCHAR(255) DEFAULT 'Anonymous Donor',
    donor_email VARCHAR(255) DEFAULT 'noemail@disasterready.app',
    amount INT NOT NULL,
    currency VARCHAR(10) DEFAULT 'PHP',
    payment_method VARCHAR(50),
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



//new table for donations

ALTER TABLE users
ADD COLUMN login_attempts INT DEFAULT 0,
ADD COLUMN last_attempt_at DATETIME NULL;


