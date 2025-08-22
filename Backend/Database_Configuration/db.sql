-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 08:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Character settings
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 /*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database: `employee_management`
-- --------------------------------------------------------

-- ------------------------
-- Table: locations
-- ------------------------
CREATE TABLE `locations` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `location_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `location_name` (`location_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data
INSERT INTO `locations` (`id`, `location_name`) VALUES
(1, 'New York'),
(2, 'San Francisco');

-- ------------------------
-- Table: departments
-- ------------------------
CREATE TABLE `departments` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `location_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `departments_location_fk` (`location_id`),
  CONSTRAINT `departments_location_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data
INSERT INTO `departments` (`id`, `name`, `location_id`) VALUES
(1, 'Human Resources', 1),
(2, 'Technology', 2);

-- ------------------------
-- Table: admins
-- ------------------------
CREATE TABLE `admins` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `mobile_number` VARCHAR(20) DEFAULT NULL,
  `employee_code` VARCHAR(50) DEFAULT NULL,
  `time_zone` VARCHAR(50) DEFAULT NULL,
  `license` INT DEFAULT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'admin',
  `lang` VARCHAR(5) NOT NULL DEFAULT 'en',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data
INSERT INTO `admins` (`id`, `first_name`, `last_name`, `email`, `password`, `mobile_number`, `employee_code`, `time_zone`, `license`, `role`, `lang`) VALUES
(1, 'Admin', 'EmpMonitor', 'admin@mail.com', '939c78c19263c8ec0b9e5fb85d7bf73d:5a5a948f63a8d406ed6af916a4928a3b', '1234567890', 'Admin', 'UTC',5, 'admin', 'en');

-- ------------------------
-- Table: employees
-- ------------------------
CREATE TABLE `employees` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(255) NOT NULL,
  `last_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `mobile_number` VARCHAR(20) DEFAULT NULL,
  `employee_code` VARCHAR(50) DEFAULT NULL,
  `time_zone` VARCHAR(50) DEFAULT NULL,
  `department_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `location_id` BIGINT(20) UNSIGNED DEFAULT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'employee',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employees_department_fk` (`department_id`),
  KEY `employees_location_fk` (`location_id`),
  CONSTRAINT `employees_department_fk` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_location_fk` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data
INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `password`, `mobile_number`, `employee_code`, `time_zone`, `role`, `department_id`, `location_id`) VALUES
(1, 'First', 'User', 'first_user@mail.com', '0c983a83e372531ee7e6b074a4cdaac4:9a3a6acdc34b7bc985753058dfb02e7f', '1234567890', 'EMP-1', 'UTC',  'employee', 2, 2);

-- ----------------------------------------------------
-- Table: employee_attendance
-- ----------------------------------------------------
CREATE TABLE `employee_attendance` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `employee_id` BIGINT(20) UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_attendance_fk` (`employee_id`),
  CONSTRAINT `employee_attendance_fk` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample Data
INSERT INTO `employee_attendance` (`id`, `employee_id`, `date`, `start_time`, `end_time`) VALUES
(1, 1, '2025-04-01', '2025-04-01 12:06:47', '2025-04-01 13:40:48'),
(2, 1, '2025-04-02', '2025-04-02 05:46:19', '2025-04-02 06:28:18');

COMMIT;

-- Restore character settings
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
 /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
 /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
