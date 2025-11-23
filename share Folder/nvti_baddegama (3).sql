-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 30, 2025 at 06:21 PM
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
(1, 'ITD00', 'Diploma in Information Technology', 4, 'Full-time', 'GCE A/L', 12, 'LKR 80,000', 'A comprehensive diploma in IT.', 'course_1761843433_690398e90bcba.png', 'active'),
(3, 'CS00', 'Web Development', 4, 'Full-time', 'A/L', 12, '20000', 'hjhkdjflkf', 'course_1761843050_6903976aa188d.png', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `course_categories`
--

CREATE TABLE `course_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_categories`
--

INSERT INTO `course_categories` (`id`, `category_name`) VALUES
(2, 'Building and Construction'),
(3, 'Electrical and Electronic'),
(4, 'Hotel and Tourism'),
(1, 'ICT and Multimedia Technology'),
(5, 'Personal Community Development');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image_name` text DEFAULT 'img_vta',
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image_name`, `image_path`, `created_at`) VALUES
(1, 'test 1', '../uploads/gallery/69039c6326ce8_1761844323.png', '2025-10-30 17:12:03'),
(3, 'frtgb', '../uploads/gallery/69039e0d5b0f4_1761844749.png', '2025-10-30 17:19:09'),
(4, 'test 12', '../uploads/gallery/69039e5669eb7_1761844822.jpg', '2025-10-30 17:20:22'),
(5, 'test 1hg', '../uploads/gallery/69039e6650456_1761844838.jpg', '2025-10-30 17:20:38'),
(6, 'fth', '../uploads/gallery/69039e731c06a_1761844851.jpg', '2025-10-30 17:20:51'),
(7, 'tyyhj', '../uploads/gallery/69039e7cf33f7_1761844860.jpg', '2025-10-30 17:21:01'),
(8, 'ftghff', '../uploads/gallery/69039e8390dc2_1761844867.jpg', '2025-10-30 17:21:07'),
(9, 'vfgh', '../uploads/gallery/69039e8a4cf28_1761844874.jpg', '2025-10-30 17:21:14');

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `module_name` varchar(150) NOT NULL,
  `module_description` text DEFAULT NULL,
  `order_no` varchar(20) DEFAULT NULL,
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
  `contact_no` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) NOT NULL,
  `course_no` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `login_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `service_id`, `first_name`, `last_name`, `nic`, `contact_no`, `email`, `gender`, `password`, `position`, `course_no`, `profile_photo`, `status`, `login_status`) VALUES
(4, 'NVTI-2025-7386', 'SD0003', 'Chamika', 'Sandeepa', '200625103462', '0771234567', 'chamika@nvti.lk', 'Male', '$2y$10$9Xx2GpPa.P3LKGW/jIQsX.wHkabsZxlwjzrY4AWPMq9EkUh67HV/.', 'Instructors', NULL, 'NVTI-STAFF-1761063260.jpg', 'active', 0),
(5, 'NVTI-2025-4768', 'SD0004', 'manu', 'Nimana', '200625103467', '0771234568', 'manu@nvti.lk', 'Male', '$2y$10$UXYnBRRstKV4hVQgMSw58epoTV6oM3Lds/jHpPTKFy4pS.wd6R/M6', 'Instructors', NULL, 'NVTI-STAFF-1761064514.png', 'active', 0),
(6, 'NVTI-2025-2872', 'S12349', 'Dayal', 'Tharinda', '200331912060', NULL, NULL, 'Male', '$2y$10$ml7lE0NudZIL/VIw/h86zuydpbsRZmxw/WHCRF2Zp6OUkuEBWX/ca', 'Instructors', NULL, 'NVTI-STAFF-1761678560.jpg', 'active', 0),
(7, 'NVTI-2025-4234', 'S12309', 'Lochana', 'Nimna', '200600912445', '0703102312', 'nadeeshnuwantha@gmail.com', 'Male', '$2y$10$0sb8JEAYAGgXtF1DMXkqy.at4QTnM.l0jj1HJwFvL2swgdZRWV0nC', 'Non-Academic Staff', NULL, 'NVTI-STAFF-1761681594.png', 'active', 0),
(8, 'NVTI-2025-3559', 'S123450', 'Ravindu', 'Chandeepa', '747813292v', '0703102312', 'nadeeshnujhy@gmail.com', 'Male', '$2y$10$e2IJ4jyk.XdvPIWGjauAe.047gtTeqf5ieYxcQTb0rZ4eEgYHo586', 'Instructors', NULL, NULL, 'active', 0),
(9, 'NVTI-2025-3218', 'S123111', 'Test', 'Staff', '747812323v', '0703102312', 'nadeeshgfs@gmail.com', 'Male', '$2y$10$yX0wHzbD8tGsqTOj2CUkPeoN8bV5Npjk3YVH2EdvfOPbdukANMxHm', 'Non-Academic Staff', NULL, 'NVTI-STAFF-1761806505.png', 'active', 0),
(10, 'NVTI-2025-2671', 'frtf', 'sanjana', 'supun', '111111111111', '0771234567', 'fcgb@fdgbfh', 'Male', '$2y$10$aTjuj/EHI8Npwvx9rblei.raGcM/kZ53qDGq7qbp8iBK7w36Esv9a', 'Instructors', 'ITD00', NULL, 'active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollments`
--

CREATE TABLE `student_enrollments` (
  `id` int(10) UNSIGNED NOT NULL,
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
-- Indexes for table `course_categories`
--
ALTER TABLE `course_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
