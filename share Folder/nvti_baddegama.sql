-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 30, 2025 at 03:54 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nvti_baddegama`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `serviceid` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `serviceid` (`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `email`, `password`, `serviceid`, `type`) VALUES
('Admin', 'Admin@gmail.com', '$2y$10$lPKGoakT1SO5yJ7sF4fBXuA.TD8s4ibWb4GAlHLmNGiIU2I0wePga', 'S001', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_no` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `course_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `nvq_level` int NOT NULL,
  `course_type` enum('Full-time','Part-time','Online','Hybrid') COLLATE utf8mb4_general_ci NOT NULL,
  `qualifications` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `course_duration` int NOT NULL,
  `course_fee` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `course_description` text COLLATE utf8mb4_general_ci NOT NULL,
  `course_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_no` (`course_no`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_no`, `course_name`, `nvq_level`, `course_type`, `qualifications`, `course_duration`, `course_fee`, `course_description`, `course_image`, `status`) VALUES
(1, 'ITD001', 'Diploma in Information Technology', 4, 'Full-time', 'GCE A/L', 12, 'LKR 80,000', 'A comprehensive diploma in IT.', NULL, 'active'),
(3, 'CS001', 'Web Development', 4, 'Full-time', 'A/L', 12, '20000', 'hjhkdjflkf', 'course_img_68f34bcc4740c_1760775116.png', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `module_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `module_description` text COLLATE utf8mb4_general_ci,
  `order_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_course_module` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `service_id` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nic` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `course_no` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_id` (`staff_id`),
  UNIQUE KEY `service_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `service_id`, `first_name`, `last_name`, `nic`, `contact_no`, `email`, `gender`, `password`, `position`, `course_no`, `profile_photo`, `status`) VALUES
(4, 'NVTI-2025-7386', 'SD0003', 'Chamika', 'Sandeepa', '200625103462', '0771234567', 'chamika@nvti.lk', 'Male', '$2y$10$9Xx2GpPa.P3LKGW/jIQsX.wHkabsZxlwjzrY4AWPMq9EkUh67HV/.', 'Instructors', 'ITD001', 'NVTI-STAFF-1761063260.jpg', 'active'),
(5, 'NVTI-2025-4768', 'SD0004', 'manu', 'Nimana', '200625103467', '0771234568', 'manu@nvti.lk', 'Male', '$2y$10$UXYnBRRstKV4hVQgMSw58epoTV6oM3Lds/jHpPTKFy4pS.wd6R/M6', 'Instructors', 'CS001', 'NVTI-STAFF-1761064514.png', 'active'),
(6, 'NVTI-2025-2872', 'S12349', 'Dayal', 'Tharinda', '200331912060', NULL, NULL, 'Male', '$2y$10$ml7lE0NudZIL/VIw/h86zuydpbsRZmxw/WHCRF2Zp6OUkuEBWX/ca', 'Instructors', 'CS001', 'NVTI-STAFF-1761678560.jpg', 'active'),
(7, 'NVTI-2025-4234', 'S12309', 'Lochana', 'Nimna', '200600912445', '0703102312', 'nadeeshnuwantha@gmail.com', 'Male', '$2y$10$0sb8JEAYAGgXtF1DMXkqy.at4QTnM.l0jj1HJwFvL2swgdZRWV0nC', 'Non-Academic Staff', NULL, 'NVTI-STAFF-1761681594.png', 'active'),
(8, 'NVTI-2025-3559', 'S123450', 'Ravindu', 'Chandeepa', '747813292v', '0703102312', 'nadeeshnujhy@gmail.com', 'Male', '$2y$10$e2IJ4jyk.XdvPIWGjauAe.047gtTeqf5ieYxcQTb0rZ4eEgYHo586', 'Instructors', 'CS001', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

DROP TABLE IF EXISTS `student_enrollments`;
CREATE TABLE IF NOT EXISTS `student_enrollments` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `Student_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nic` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `dob` date NOT NULL,
  `contact_no` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `whatsapp_no` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ol_pass_status` enum('Yes','No') COLLATE utf8mb4_general_ci NOT NULL,
  `ol_english_grade` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ol_maths_grade` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ol_science_grade` varchar(5) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `al_category` enum('science','commerce','arts','tech','other') COLLATE utf8mb4_general_ci NOT NULL,
  `course_option_one` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `course_option_two` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `Student_id`, `full_name`, `nic`, `address`, `dob`, `contact_no`, `whatsapp_no`, `ol_pass_status`, `ol_english_grade`, `ol_maths_grade`, `ol_science_grade`, `al_category`, `course_option_one`, `course_option_two`, `application_date`, `is_processed`) VALUES
(1, 'VTA_BAD123456', 'Nimal Silva', '981234567V', 'No. 12, Main Street, Galle', '1998-05-10', '0771234567', '0771234567', 'Yes', 'B', 'C', 'C', 'tech', 'graphic_design', 'it_diploma', '2025-10-10 07:03:21', 0),
(2, 'VTA_BAD654321', 'Saman Kumara', '990123456V', 'Temple Road, Baddegama', '1999-02-20', '0717654321', '', 'Yes', 'A', 'A', 'A', 'science', 'it_diploma', 'graphic_design', '2025-10-11 08:11:17', 1),
(3, 'VTA_BAD392346', 'fbfgbf', '200625103468', 'fgfrdgdgdg', '2025-10-21', '0704071099', '0704071099', 'Yes', 'B', 'C', 'F', 'tech', 'Diploma in Information Technology', 'Diploma in Information Technology', '2025-10-21 17:07:29', 0),
(4, 'VTA_BAD459232', 'A.Ravindu Chandeepa', '200400912445', 'No.615, yaya 01, Sooriyawewa', '2004-01-09', '0782509946', '0782509946', 'Yes', 'C', 'A', 'B', '', 'Diploma in Information Technology', 'Web Development', '2025-10-28 19:56:57', 0),
(6, 'VTA_BAD780611', 'A.Ravindu Chandeepa', '200400912445', 'HFDUDTRY', '2004-01-09', '0782509946', '0782509946', 'Yes', 'C', 'A', 'C', '', 'Diploma in Information Technology', 'Web Development', '2025-10-29 14:42:25', 0),
(7, 'VTA_BAD761934', 'Chamika', '200109800987', 'Test', '2025-11-13', '0711234567', '0711234567', 'Yes', 'A', 'A', 'B', '', 'Diploma in Information Technology', 'Diploma in Information Technology', '2025-10-29 14:50:37', 0),
(8, 'VTA_BAD150305', 'Nadheesh', '200500912445', 'qwertyu', '2025-10-13', '0702509946', '0702509946', 'Yes', 'B', 'B', 'B', '', 'Diploma in Information Technology', 'Web Development', '2025-10-29 14:53:45', 0),
(9, 'VTA_BAD858839', 'Test', '200109854355', 'Test', '2025-10-12', '0711234568', '0711234568', 'Yes', 'A', 'C', 'C', '', 'Web Development', 'Diploma in Information Technology', '2025-10-29 15:11:26', 0),
(10, 'VTA_BAD718822', 'Test', '123456789012', 'Test', '2025-10-01', '0781236789', '0781236789', 'Yes', 'S', 'B', 'A', 'arts', 'Diploma in Information Technology', 'Diploma in Information Technology', '2025-10-29 15:49:45', 0),
(11, 'VTA_BAD834077', 'Test', '200109100965', 'Test', '2025-10-01', '0912244567', '0912244567', 'Yes', 'A', 'B', 'C', 'tech', 'Diploma in Information Technology', 'Web Development', '2025-10-29 16:27:27', 0),
(12, 'VTA_BAD996338', 'Chamika Test', '200456723456', 'Baddegama', '2001-03-10', '0778128743', '0778128743', 'Yes', 'A', 'A', 'B', 'commerce', 'Diploma in Information Technology', 'Web Development', '2025-10-29 16:35:37', 0);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `fk_course_module` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
