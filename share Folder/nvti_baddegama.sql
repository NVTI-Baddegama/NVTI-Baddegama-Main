-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2025 at 12:37 PM
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
-- Database: `nvti_baddegama`
--

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'Unique ID for each enrollment record',
  `Student_id` varchar(50) NOT NULL COMMENT 'Auto Generated Student ID ',
  `full_name` varchar(255) NOT NULL COMMENT 'Full Name (fullName)',
  `nic` varchar(12) NOT NULL COMMENT 'National Identity Card (nic)',
  `address` text NOT NULL COMMENT 'Permanent Address (address)',
  `dob` date NOT NULL COMMENT 'Date of Birth (dob)',
  `contact_no` varchar(15) NOT NULL COMMENT 'Primary Contact Number (contactNo)',
  `whatsapp_no` varchar(15) DEFAULT NULL COMMENT 'Optional WhatsApp Number (whatsappNo)',
  `ol_pass_status` enum('Yes','No') NOT NULL COMMENT 'G.C.E. O/L Pass Status (olPassStatus)',
  `ol_english_grade` varchar(5) DEFAULT NULL COMMENT 'O/L English Grade (olEnglish)',
  `ol_maths_grade` varchar(5) DEFAULT NULL COMMENT 'O/L Mathematics Grade (olMaths)',
  `ol_science_grade` varchar(5) DEFAULT NULL COMMENT 'O/L Science Grade (olScience)',
  `al_category` enum('science','commerce','arts','tech','other') NOT NULL COMMENT 'G.C.E. A/L Stream/Category (alCategory)',
  `course_option_one` varchar(50) NOT NULL COMMENT 'Primary Course Choice (courseOptionOne)',
  `course_option_two` varchar(50) DEFAULT NULL COMMENT 'Secondary Course Choice (courseOptionTwo)',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Date and time of submission',
  `is_processed` tinyint(1) DEFAULT 0 COMMENT 'Flag to track if the application has been processed (0=No, 1=Yes)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores enrollment data for NVTI Baddegama';

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `Student_id`, `full_name`, `nic`, `address`, `dob`, `contact_no`, `whatsapp_no`, `ol_pass_status`, `ol_english_grade`, `ol_maths_grade`, `ol_science_grade`, `al_category`, `course_option_one`, `course_option_two`, `application_date`, `is_processed`) VALUES
(1, '', 'fbfgbf', '200625103469', 'fklejiofewfjfjedwfoerjfio', '2025-10-08', '0704071099', '0704071099', 'Yes', 'B', 'C', 'C', 'tech', 'graphic_design', 'hnd_business', '2025-10-10 07:03:21', 0),
(2, 'SUD181490', 'fbfgbf', '200625103462', 'wedwqdw', '2025-10-21', '0704071099', '', 'Yes', 'A', 'A', 'A', 'science', 'hnd_business', 'it_diploma', '2025-10-10 07:11:17', 0),
(3, 'VTA-BAD636612', 'fbfgbf', '200625103466', 'scdsadsa', '2025-10-08', '0704071099', '0704071099', 'Yes', 'C', 'B', 'C', 'tech', 'graphic_design', 'it_diploma', '2025-10-10 07:25:58', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic_unique` (`nic`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for each enrollment record', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
