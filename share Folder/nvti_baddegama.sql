-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 23, 2025 at 05:10 AM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `serviceid` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`username`, `email`, `password`, `serviceid`) VALUES
('Admin', 'Admin@gmail.com', '$2y$10$qYIpCAyjePV6qU72TmRaL.ZnEE.5PoLcfp2q7X7JyWTFvergopVAO', 'S001');

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
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_no`, `course_name`, `nvq_level`, `course_type`, `qualifications`, `course_duration`, `course_fee`, `course_description`, `status`) VALUES
(1, 'ITD001', 'Diploma in Information Technology', 4, 'Full-time', 'GCE A/L', 12, 'LKR 80,000', 'A comprehensive diploma in IT.', 'active'),
(2, 'GD001', 'Certificate in Graphic Design', 3, 'Part-time', 'GCE O/L', 6, 'LKR 45,000', 'Learn the fundamentals of graphic design.', 'active');

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
(1, 'NVTI-2025-1234', 'ST001', 'Kamal', 'Perera', '901234567V', 'Male', '$2y$10$s.oFbLhzgmIu4DVUVPk9tuK9YXwPA6xJwU6WQEyizu7KXWrJhEeV', 'Instructors', 'ITD001', NULL, 'active'),
(2, 'NVTI-2025-4914', 'SD0001', 'Chamika', 'Sandeepa', '200625103469', 'Male', '$2y$10$LoAEzXAqKAGlG9sReTDNFuAPbYXwuExyJfcRI6oIyMlp3lI7HPYbu', 'Instructors', 'GD001', 'NVTI-STAFF-1761125168.jpeg', 'active'),
(3, 'NVTI-2025-3722', 'SD0002', 'manu', 'Nimana', '200625103468', 'Male', '$2y$10$..b5rISFZN7EQBqodwYQLexABVy57h1qb0NPg1VWRIKa80zFoqQ4i', 'Non-Academic Staff', NULL, 'NVTI-STAFF-1761125298.jpeg', 'active');

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
  `al_category` varchar(50) NOT NULL,
  `course_option_one` varchar(100) NOT NULL,
  `course_option_two` varchar(100) DEFAULT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_processed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_enrollments`
--

INSERT INTO `student_enrollments` (`id`, `Student_id`, `full_name`, `nic`, `address`, `dob`, `contact_no`, `whatsapp_no`, `ol_pass_status`, `ol_english_grade`, `ol_maths_grade`, `ol_science_grade`, `al_category`, `course_option_one`, `course_option_two`, `application_date`, `is_processed`) VALUES
(1, 'VTA_BAD123456', 'Nimal Silva', '981234567V', 'No. 12, Main Street, Galle', '1998-05-10', '0771234567', '0771234567', 'Yes', 'B', 'C', 'C', 'tech', 'Certificate in Graphic Design', 'Diploma in Information Technology', '2025-10-10 07:03:21', 0),
(2, 'VTA_BAD654321', 'Saman Kumara', '990123456V', 'Temple Road, Baddegama', '1999-02-20', '0717654321', '', 'Yes', 'A', 'A', 'A', 'science', 'Diploma in Information Technology', 'Certificate in Graphic Design', '2025-10-11 08:11:17', 1);

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
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `service_id` (`service_id`),
  ADD UNIQUE KEY `nic` (`nic`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
