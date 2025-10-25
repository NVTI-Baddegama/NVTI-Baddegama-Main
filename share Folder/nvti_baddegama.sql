-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2025 at 06:55 PM
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
-- Database: `nvti_baddegama`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `serviceid` varchar(11) NOT NULL,
  `type` varchar(11) NOT NULL
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

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `course_no` varchar(12) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `nvq_level` int(11) NOT NULL,
  `course_type` enum('Full-time','Part-time','Online','Hybrid') NOT NULL,
  `qualifications` varchar(255) NOT NULL,
  `course_duration` int(11) NOT NULL,
  `course_fee` varchar(20) NOT NULL,
  `course_description` text NOT NULL,
  `course_image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `module_name` varchar(150) NOT NULL,
  `module_description` text DEFAULT NULL,
  `order_no` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `staff_id` varchar(20) NOT NULL,
  `service_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `nic` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `course_no` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `service_id`, `first_name`, `last_name`, `nic`, `gender`, `password`, `position`, `course_no`, `profile_photo`, `status`) VALUES
(4, 'NVTI-2025-7386', 'SD0003', 'Chamika', 'Sandeepa', '200625103462', 'Male', '$2y$10$9Xx2GpPa.P3LKGW/jIQsX.wHkabsZxlwjzrY4AWPMq9EkUh67HV/.', 'Instructors', 'ITD001', 'NVTI-STAFF-1761063260.jpg', 'active'),
(5, 'NVTI-2025-4768', 'SD0004', 'manu', 'Nimana', '200625103467', 'Male', '$2y$10$UXYnBRRstKV4hVQgMSw58epoTV6oM3Lds/jHpPTKFy4pS.wd6R/M6', 'Instructors', 'CS001', 'NVTI-STAFF-1761064514.png', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(11) UNSIGNED NOT NULL,
  `Student_id` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `nic` varchar(12) NOT NULL,
  `address` text NOT NULL,
  `dob` date NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `whatsapp_no` varchar(15) DEFAULT NULL,
  `ol_pass_status` enum('Yes','No') NOT NULL,
  `ol_english_grade` varchar(5) DEFAULT NULL,
  `ol_maths_grade` varchar(5) DEFAULT NULL,
  `ol_science_grade` varchar(5) DEFAULT NULL,
  `al_category` enum('science','commerce','arts','tech','other') NOT NULL,
  `course_option_one` varchar(100) NOT NULL,
  `course_option_two` varchar(100) DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_processed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `Student_id`, `full_name`, `nic`, `address`, `dob`, `contact_no`, `whatsapp_no`, `ol_pass_status`, `ol_english_grade`, `ol_maths_grade`, `ol_science_grade`, `al_category`, `course_option_one`, `course_option_two`, `application_date`, `is_processed`) VALUES
(1, 'VTA_BAD123456', 'Nimal Silva', '981234567V', 'No. 12, Main Street, Galle', '1998-05-10', '0771234567', '0771234567', 'Yes', 'B', 'C', 'C', 'tech', 'graphic_design', 'it_diploma', '2025-10-10 07:03:21', 0),
(2, 'VTA_BAD654321', 'Saman Kumara', '990123456V', 'Temple Road, Baddegama', '1999-02-20', '0717654321', '', 'Yes', 'A', 'A', 'A', 'science', 'it_diploma', 'graphic_design', '2025-10-11 08:11:17', 1),
(3, 'VTA_BAD392346', 'fbfgbf', '200625103468', 'fgfrdgdgdg', '2025-10-21', '0704071099', '0704071099', 'Yes', 'B', 'C', 'F', 'tech', 'Diploma in Information Technology', 'Diploma in Information Technology', '2025-10-21 17:07:29', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `serviceid` (`serviceid`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_no` (`course_no`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_course_module` (`course_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `service_id` (`service_id`);

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
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
