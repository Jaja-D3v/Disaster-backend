-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 01:00 PM
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
-- Table structure for table `archived_users`
--

CREATE TABLE `archived_users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `login_attempts` int(11) DEFAULT NULL,
  `last_attempt_at` datetime DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archived_users`
--

INSERT INTO `archived_users` (`id`, `username`, `email`, `password`, `role`, `status`, `barangay`, `last_logged_in`, `created_at`, `updated_at`, `login_attempts`, `last_attempt_at`, `archived_at`) VALUES
(13, 'Neo', 'jaredabrera344@gmail.com', '$2y$10$OwWdSnlaKVRwTBHPXZ/0QeN08jyKHfaLWj9Jhx0.POS2wwQBG8a5K', 2, 'deactivated', 'Central', '2025-10-30 15:54:29', '2025-10-29 18:30:34', '2025-10-30 15:54:29', 0, NULL, '2025-10-30 15:54:47');

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
(1, 'Updated Barangay', '09171234567', '1234567', 'testuser@gmail.com', 'https://facebook.com/updatedbarangay', 'Juan Dela Cruz', 'Maria Santos', 14.5995000, 120.98420000, '2025-10-29 10:38:53', 1500, 160000, 800, 200, 250, 300, 'Barangay census 2025', '2025-10-29', NULL),
(15, 'Barangay Test', '09123456789', '1234567', 'test@barangay.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:45:45', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(16, 'Barangay Test', '09123456789', '1234567', 'test@gmail.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:49:52', 120, 60000, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(17, 'Barangay Test', '09123456789', '1234567', 'test@askjda.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 11:52:07', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(18, 'Barangay Test', '09123456789', '1234567', 'phonetest@smart.com.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:14', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(19, 'Barangay Test', '09123456789', '1234567', 'testuser@gmail.com', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:34', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(20, 'Barangay Test', '09123456789', '1234567', 'testuser@deped.gov.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:03:51', 120, 130, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12),
(21, 'Barangay Test', '09123456789', '1234567', 'student@ust.edu.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:04:16', 120, 130, 50, 20, 25, 15506050, 'PSA Census 2020', '2025-10-29', 12),
(22, 'Barangay Test', '09234563789', '1234567', 'teacher@ust.edu.ph', 'https://facebook.com/barangaytest', 'Juan Dela Cruz', 'Maria Santos', 14.6560000, 121.03100000, '2025-10-29 12:06:43', 8, 130000, 50, 20, 25, 15, 'PSA Census 2020', '2025-10-29', 12);

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
(12, 'pi_5Eg8ahY236fmHRrHnM6dQMAk', 'ZGJf2TUFWd9vSQo4vKE5ToQu05q4PJ2zc5V7rb55nnM=', 'iKehfBo5GzFvybY5fY9hXfGAqTIEIycluaB2dEJ9A7KlmcrNlhbLvTi4BbGFWeYl', 2000, 'PHP', 'gcash', 'pending', '2025-10-30 01:46:08'),
(13, 'pi_WP3kPRjHuVxzk7uu58iVabaW', 'Rk3RhNWhTx9BKsct4xZ44jp7JSjqThgX6/qKLnqNlzY=', '/txNzthvnGAFINYhTjGwhv885NECcmYbMgL4k/gLK3w/jwtm0V04rXZWgSDStIdB', 2000, 'PHP', 'gcash', 'pending', '2025-11-01 06:02:01'),
(14, 'pi_xiHHxVxuxeo6BdCBasgLr9hE', 'N4R3fy1Kkhjid5OnutdQKlrqTI5uh74W69zSuAuYERE=', 'LN5xT7KksRxtEOZdp17aNElOZyKt+QltHxdia0gV/8VhsFJA1tKPOYco2jK+G18x', 2000, 'PHP', 'gcash', 'pending', '2025-11-01 06:20:44'),
(15, 'pi_xcDBDQcdjUMaXhqcnM8Q1cC1', 'Aj4idmfR1hqyV424EV9hi5zJpyemqZUDTrsD56CoOxw=', 'sy0vfcnpNI0/4pJCPwYtp66YQtO3cLfplly1vd5JLGzHxt0JSO/2TJ56f2HPudvv', 2000, 'PHP', 'gcash', 'pending', '2025-11-01 06:25:41'),
(16, 'pi_PRXmsBZoNcK6HkU4hi9UXDco', '4Q0POhw8m5JsQyJlqriOq9UJ6jlHHDh+nTbLlEUtUco=', 'xMJMOJlSlsPmUn2WJWwLA/oPfHvdZqf49wfi4RANR2ii50u98dQY5u42sadFwO2w', 2000, 'PHP', 'gcash', 'pending', '2025-11-04 11:15:26');

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

INSERT INTO `evacuation_center` (`id`, `name`, `location`, `capacity`, `current_evacuees`, `contact_person`, `contact_number`, `lat`, `long`, `created_at`, `updated_at`, `created_by`) VALUES
(23, 'sample lang', 'mayondon', 500, 34, 'noel lantaca', '09485849345', 14.17502197, 121.24039493, '2025-11-03 03:00:47', '2025-11-03 03:00:47', 'abrera, putho-tuntungin'),
(24, 'sample lang', 'batong malake', 500, 43, 'redniel lnoe', '09847576857', 14.17585331, 121.23876137, '2025-11-03 11:18:48', '2025-11-03 11:18:48', 'abrera, putho-tuntungin');

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
(1, 'dsfsbdfsdfj', '639943910539', 'sdjfsdfsbdjfdsbfsjdf', 'asdbasjdmas', 'Ongoing', 'Jared Abrera', '2025-10-29 10:15:05', '2025-10-29 10:21:18', NULL, NULL, 'Minor'),
(2, 'John Doe', '09123456789', 'Test inciden', 'incidentPhotos/1761885296_Blue and Yellow Gradient Modern Desktop Wallpaper.jpg', 'Pending', NULL, '2025-10-31 04:34:56', '2025-10-31 04:34:56', 14.59950000, 120.98420000, ''),
(3, 'John Doe', '09123456789', 'Test inciden', 'incidentPhotos/1761976893_Blue and Yellow Gradient Modern Desktop Wallpaper.jpg', 'Pending', NULL, '2025-11-01 06:01:33', '2025-11-01 06:01:33', 14.59950000, 120.98420000, ''),
(4, 'John Doe', '09123456789', 'Test inciden', 'incidentPhotos/1761978373_Blue and Yellow Gradient Modern Desktop Wallpaper.jpg', 'Pending', NULL, '2025-11-01 06:26:13', '2025-11-01 06:26:13', 14.59950000, 120.98420000, '');

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

INSERT INTO `password_history` (`id`, `user_id`, `password`, `created_at`) VALUES
(6, 16, '$2y$10$CE4GMW8Uwu3SQcb3gZxwGuWfVmsp10KV1NDdMBN0HhsQU27ZP7L7q', '2025-11-05 05:55:53');

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

--
-- Dumping data for table `password_request_logs`
--

INSERT INTO `password_request_logs` (`id`, `ip_address`, `email`, `created_at`) VALUES
(1, '::1', 'jaredabrera44@gmail.com', '2025-10-31 20:27:42'),
(2, '::1', 'jaredabrera44@gmail.com', '2025-11-03 09:17:13'),
(3, '::1', 'jaredabrera44@gmail.com', '2025-11-03 09:19:58'),
(4, '::1', 'jaredabrera44@gmail.com', '2025-11-03 19:04:30'),
(5, '::1', 'jaredabresdkra344@gmail.com', '2025-11-04 19:03:42'),
(6, '::1', 'jaredabresdksdfra344@gmail.com', '2025-11-04 19:03:50'),
(7, '::1', 'jaredabresdksdffdsra344@gmail.com', '2025-11-04 19:03:58'),
(8, '::1', 'jaredabresdksdffdsra344@gmail.com', '2025-11-04 19:04:04'),
(9, '::1', 'jaredabresdksdffdssdfra344@gmail.com', '2025-11-04 19:04:06'),
(10, '::1', 'jaredabrera44@gmail.com', '2025-11-05 13:34:54'),
(11, '::1', 'jaredabrera44@gmail.com', '2025-11-05 13:54:23'),
(12, '::1', 'jaredabrera344@gmail.com', '2025-11-05 14:43:14'),
(13, '::1', 'jaredabrera44@gmail.com', '2025-11-05 15:39:52'),
(14, '::1', 'jaredabrera44@gmail.com', '2025-11-05 15:41:56'),
(15, '::1', 'jaredabrera44@gmail.com', '2025-11-05 15:42:57'),
(16, '::1', 'jaredabrera44@gmail.com', '2025-11-05 15:43:14'),
(17, '::1', 'jaredabrera4@gmail.com', '2025-11-05 15:43:33'),
(18, '::1', 'jaredabrera4@gmail.com', '2025-11-05 15:44:00'),
(19, '::1', 'jaredabrera44@gmail.com', '2025-11-06 14:45:45'),
(20, '::1', 'jaredabrera44@gmail.com', '2025-11-06 18:27:52'),
(21, '::1', 'jaredabrera44@gmail.com', '2025-11-06 18:40:57');

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

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(10, 'jaredabrera44@gmail.com', '97140bb8308f63f21aad47e180574c92', '2025-11-05 15:48:14'),
(12, 'jaredabrera4@gmail.com', 'fa4c98554432ee610e8b423160d1cd51', '2025-11-05 15:49:00');

-- --------------------------------------------------------

--
-- Table structure for table `pending_account_request`
--

CREATE TABLE `pending_account_request` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `barangay` varchar(255) DEFAULT NULL,
  `last_logged_in` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `login_attempts` int(11) DEFAULT 0,
  `last_attempt_at` datetime DEFAULT NULL
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
-- Table structure for table `relief_packs`
--

CREATE TABLE `relief_packs` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `total_packs` int(11) NOT NULL,
  `date_input` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relief_packs`
--

INSERT INTO `relief_packs` (`id`, `description`, `total_packs`, `date_input`, `created_at`) VALUES
(22, 'Food Relief Pack - October 2025', 200, '2025-10-31', '2025-10-30 07:17:06'),
(23, 'Food Relief Pack - October 2025', 50, '2025-10-31', '2025-10-30 12:37:41');

-- --------------------------------------------------------

--
-- Table structure for table `relief_pack_barangays`
--

CREATE TABLE `relief_pack_barangays` (
  `id` int(11) NOT NULL,
  `relief_pack_id` varchar(255) NOT NULL,
  `barangay_id` varchar(255) NOT NULL,
  `allocated_packs` int(11) NOT NULL,
  `created_at` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relief_pack_barangays`
--

INSERT INTO `relief_pack_barangays` (`id`, `relief_pack_id`, `barangay_id`, `allocated_packs`, `created_at`) VALUES
(1, 'Food Relief Pack - October 2025', 'Updated Barangay', 800, 2147483647),
(2, 'Food Relief Pack - October 2025', 'Barangay Test', 50, 2147483647),
(3, 'Food Relief Pack - October 2025', 'Barangay Test', 50, 2147483647);

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

INSERT INTO `road_advisories` (`id`, `title`, `details`, `date_time`, `status`, `added_by`) VALUES
(6, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'hsdbfjdsf', 'putho-tuntungin'),
(7, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'hsdbfjdsf', '12'),
(8, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'hsdbfjdsf', '12'),
(9, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'asjdnkajda', '12'),
(10, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'hsdbfjdsf', 'putho-tuntungin');

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
(12, 'jared', 'jaredabrera4@gmail.com', '$2y$10$OwWdSnlaKVRwTBHPXZ/0QeN08jyKHfaLWj9Jhx0.POS2wwQBG8a5K', 1, 'active', 'Central', '2025-11-01 16:18:46', '2025-10-29 10:28:40', '2025-11-06 10:44:46', 3, '2025-11-03 10:20:25'),
(15, 'NEO', 'jaredabrera454@gmail.com', '$2y$10$cCyVld4UxxLVfI798Ngts.YWpcPt7ag7DtsutRqqBIbqn7vAbeYDy', 2, 'active', 'Central', '2025-10-31 21:03:22', '2025-10-29 10:28:40', '2025-11-06 08:30:55', 3, '2025-11-06 16:30:55'),
(16, 'abrera', 'jaredabrera54@gmail.com', '$2y$10$OwWdSnlaKVRwTBHPXZ/0QeN08jyKHfaLWj9Jhx0.POS2wwQBG8a5K', 1, 'active', 'putho-tuntungin', '2025-11-06 18:46:13', '2025-11-03 01:16:33', '2025-11-06 10:46:13', 0, NULL),
(18, 'testingUser', 'sds@gmail.com', '$2y$10$OwWdSnlaKVRwTBHPXZ/0QeN08jyKHfaLWj9Jhx0.POS2wwQBG8a5K', 2, 'active', 'batong-malake', '2025-11-06 18:18:19', '2025-11-06 06:46:13', '2025-11-06 10:27:11', 0, NULL),
(19, 'Mr.Abrera', 'jaredasdsbrera44@gmail.com', '$2y$10$DTBHH.KcUULKMEPiyIOpg.VrLE11drM6RUQ28flO8xJK8RTX09lM2', 2, '', 'bagong-silang', NULL, '2025-11-06 10:28:13', '2025-11-06 10:40:21', 0, NULL),
(20, 'Mr. Jared', 'jaredabrera44@gmail.com', '$2y$10$0FPwk6316FfDCnAdjwjIne2XuETJhFenMlF.DnjnJgSb78wYyDMo2', 2, '', 'san-antonio', NULL, '2025-11-06 10:41:17', '2025-11-06 10:46:19', 0, NULL);

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

INSERT INTO `weather_advisories` (`id`, `title`, `details`, `date_time`, `added_by`) VALUES
(17, 'Community Clean-up Drive', 'updated', '2025-11-03 07:00:00', '12'),
(18, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', '12'),
(19, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', '12'),
(20, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', '12'),
(21, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'putho-tuntungin'),
(22, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'putho-tuntungin'),
(23, 'Road Closure Alert', 'Main street closed due to flooding.', '2025-11-01 10:00:00', 'putho-tuntungin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archived_users`
--
ALTER TABLE `archived_users`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `pending_account_request`
--
ALTER TABLE `pending_account_request`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relief_packs`
--
ALTER TABLE `relief_packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relief_pack_barangays`
--
ALTER TABLE `relief_pack_barangays`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `disaster_mapping`
--
ALTER TABLE `disaster_mapping`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `disaster_update`
--
ALTER TABLE `disaster_update`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `evacuation_center`
--
ALTER TABLE `evacuation_center`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `incident_reports`
--
ALTER TABLE `incident_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_history`
--
ALTER TABLE `password_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `password_request_logs`
--
ALTER TABLE `password_request_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pending_account_request`
--
ALTER TABLE `pending_account_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pending_registrations`
--
ALTER TABLE `pending_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `relief_packs`
--
ALTER TABLE `relief_packs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `relief_pack_barangays`
--
ALTER TABLE `relief_pack_barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `road_advisories`
--
ALTER TABLE `road_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `weather_advisories`
--
ALTER TABLE `weather_advisories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
