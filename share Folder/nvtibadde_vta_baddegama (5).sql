-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 02, 2025 at 09:20 AM
-- Server version: 10.11.14-MariaDB-cll-lve
-- PHP Version: 8.4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nvtibadde_vta_baddegama`
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
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `category` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_no`, `course_name`, `nvq_level`, `course_type`, `qualifications`, `course_duration`, `course_fee`, `course_description`, `course_image`, `status`, `category`) VALUES
(1, 'K72S014.0', 'National Certificate - Software Developer', 4, 'Full-time', 'Pass the GCE (O/L)', 12, '31,300.00', 'The Software Developer qualification focuses on designing, coding, testing, and maintaining software applications to meet user requirements.It equips learners with practical programming skills and problem-solving abilities for careers in the IT industry.', 'course_img_690441cb38435_1761886667.jpg', 'active', '1'),
(7, 'K72T001.2', 'National Diploma - Information & Communication Technology', 5, 'Full-time', 'Passed NVQ Level 4 / NCICT or Equivalent (A/L) 3 Passed with (A/L) ICT', 12, '48,300.00', 'The NVQ ICT Level 5 qualification develops advanced skills in information and communication technology, focusing on system management and professional IT solutions.It prepares learners for higher-level technical roles and further studies in the ICT field.', 'course_img_6904367a1eb82_1761883770.jpeg', 'active', '1'),
(8, 'F45T002.1', 'National Diploma - Quantity Surveying', 5, 'Full-time', 'GCE (A/L) Mathematics or Bio-Science, Commerce with high with (O/L) Maths, English-Computer Technology', 12, '27,300.00', 'The NVQ Quantity Surveying Level 5 qualification focuses on advanced cost management, project planning, and construction contract administration.It prepares learners for professional roles in the construction industry with strong analytical and managerial skills.', 'course_img_69043c0ded1b6_1761885197.jpg', 'active', '2'),
(9, 'E40S002.3', 'National Certificate - Electronic Appliances Maintenance Technician', 4, 'Full-time', 'GCE (O/L)', 12, '25,300.00', 'The Electronic Appliances Maintenance Technician qualification trains learners to install, maintain, and repair household and industrial electronic equipment.It provides practical skills and technical knowledge required for a career in the electronics service industry.', 'course_img_69043e7faa333_1761885823.jpg', 'active', '3'),
(10, 'K72S012.0', 'National Certificate - Multimedia Designing Associate', 4, 'Full-time', 'Pass the GCE (O/L)', 12, '51,300.00', 'The Multimedia Designing Associate qualification focuses on developing creative skills in graphic design, animation, video editing, and digital media production.It prepares learners for careers in the multimedia and creative industries through practical design and software training.', 'course_img_6904404c59a2e_1761886284.jpg', 'active', '1'),
(12, 'E40S001.6', 'National Certificate - Electrician', 4, 'Full-time', 'Pass the GCE (O/L)', 12, '45,300.00', 'The Electrician qualification trains learners to install, maintain, and repair electrical systems in residential, commercial, and industrial settings.It provides the technical knowledge and safety skills needed for a professional career in the electrical field.', 'course_img_690443e3aefaf_1761887203.jpg', 'active', '3'),
(13, 'K72S015.0', 'National Certificate - Computer Hardware & Network Technician', 4, 'Full-time', 'Pass the GCE (O/L)', 12, '36,300.00', 'The Computer Hardware & Network Technician qualification focuses on assembling, troubleshooting, and maintaining computer systems and network infrastructures.It provides learners with practical skills in hardware repair, networking, and system support for IT-related careers.', 'course_img_690446541a433_1761887828.jpg', 'active', '1'),
(14, 'M80S001.3', 'National Certificate - Preschool Teacher (Early Childhood Development)', 4, 'Full-time', 'GCE (A/L) Singing / Instrument Playing / Dancing Skills', 12, '39,300.00', 'The Pre-School Teacher (Early Childhood Development) qualification focuses on nurturing young childrenâ€™s learning, creativity, and social skills through play-based education.It prepares learners to plan and deliver early childhood educational activities in a safe and caring environment.', 'course_img_6904496e4fcb5_1761888622.jpg', 'active', '5'),
(15, 'E41S001.5', 'National Certificate - Plumber', 4, 'Full-time', 'Pass the GCE (O/L)', 12, '2300.00', 'The Plumber qualification trains learners to install, repair, and maintain plumbing systems in residential, commercial, and industrial settings.It provides practical skills and technical knowledge required for a professional career in plumbing and water system management.', 'course_img_69044f7e2f73c_1761890174.jpg', 'active', '2'),
(16, 'H55S010.2', 'National Certificate - Professional Cookery (Commis)', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '49,150.00', 'The Professional Cookery (Commis) qualification focuses on developing fundamental cooking skills, food preparation, and kitchen hygiene practices.It prepares learners for entry-level positions in professional kitchens, enabling them to work under supervision and gain practical culinary experience.', 'course_1761897900_69046dac61844.png', 'active', '4'),
(17, 'H55S005.1', 'National Certificate - Food and Beverage Associate', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '24,150.00', 'The Food & Beverage Associate qualification focuses on providing excellent customer service, handling food and drinks, and maintaining hygiene standards in hospitality settings.It prepares learners for entry-level roles in restaurants, hotels, and catering services, emphasizing teamwork and professional etiquette.', 'course_img_690452ba08a8f_1761891002.jpg', 'active', '4'),
(18, 'K72S004.2', 'National Certificate - Information and Communication Technology Technician', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '26,150.00', 'The ICT Technician Level 4 qualification focuses on developing practical skills in computer systems, networking, and technical support.It prepares learners to manage, maintain, and troubleshoot ICT equipment and systems in professional environments.', 'course_img_690455b87f7f1_1761891768.jpg', 'active', '1'),
(19, 'N85S024.0', 'National Certificate - Child Caregiver', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '24,150.00', 'The Child Caregiver NVQ Level 4 qualification focuses on advanced skills in child development, health, and safety for young children.It prepares learners to provide professional care and support in early childhood education and caregiving settings.', 'course_img_6904577dd2763_1761892221.jpg', 'active', '5'),
(20, 'E40S003.2', 'National Certificate - Electric Machine Winder', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '15,150.00', 'A Level 4 Electric Machine Winder can wind complex coils for industrial electric machines.They are skilled in inspecting, repairing, and maintaining high-voltage motors and generators.', 'course_img_69045d5a4efce_1761893722.jpg', 'active', '3'),
(21, 'F45S003.4', 'National Certificate - Aluminium Fabricator', 4, 'Full-time', 'GCE (O/L) Special Skills', 6, '17,150.00', 'As an NVQ Levelâ€¯4 Aluminium Fabricator you are skilled in fabricating and installing aluminium doors, windows, partitions, shopâ€‘fronts and curtainâ€‘walls, using hand and power tools with accuracy. You also take responsibility for reading working drawings, measuring and cutting materials, finishing the product to specification, and ensuring workplace health & safety practices are followed.', 'course_img_690460c25050a_1761894594.png', 'active', '2'),
(22, 'D15S002.2', 'National Certificate - Professional Baker/Commis (Pastry & Bakery)', 4, 'Full-time', 'Pass the GCE (O/L)', 6, '43,150.00', 'As a Levelâ€¯4 Baker/Commis in Pastry &â€¯Bakery, you independently prepare breads, cakes, pastries and desserts, following recipes and industry standards for quality, hygiene and presentation.You take responsibility for arranging workstations, measuring ingredients, baking and decorating a range of bakery products, and ensuring safe operation of bakery equipment.', 'course_img_690463547dd09_1761895252.jpeg', 'active', '4'),
(23, 'F45S007.2', 'National Certificate - Quantity Surveying Assistant', 4, 'Full-time', 'GCE (A/L) Mathematics or Bio-Science, Commerce with high with (O/L) Maths, English-Computer Technology', 6, '27,300.00', 'As a Levelâ€¯4 Quantity Surveying Assistant, you support costâ€‘estimating, takeâ€‘offs, tendering and contract administration under supervision of a qualified quantity surveyor.You assist in preparing bills of quantities, tracking project costs, measuring works, and maintaining accurate financial records to ensure construction projects stay on budget.', 'course_img_69046575140cd_1761895797.jpeg', 'active', '2'),
(24, 'N85S010.1', 'National Certificate - Care Giver (Elder)', 3, 'Part-time', 'GCE (O/L)', 3, '41,450.00', 'Completed NVQ Level 3 in Elder Care with practical experience in supporting elderly individuals.Skilled in providing quality care, maintaining hygiene, and promoting the wellbeing of elders.', 'course_img_69046a2115244_1761896993.jpg', 'active', '5'),
(25, 'H55S043.0', 'National Certificate - Hotel Operations Multitasker', 3, 'Part-time', 'GCE (O/L)', 3, '80,000.00', 'NVQ Level 3 in Hotel Operations with hands-on experience in front office, housekeeping, and food service.Capable of multitasking efficiently in hotel environments while maintaining high standards of guest service.', 'course_img_69046c48d7b52_1761897544.jpg', 'active', '4');

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
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `feedback` varchar(700) NOT NULL,
  `rating` int(5) NOT NULL,
  `approve` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_name`, `feedback`, `rating`, `approve`) VALUES
(7, 'Ravindra', 'This  project is excellent experience for yours group', 5, 1),
(8, 'Ravindra Lakshman', 'Your website makes a positive first impression: the layout is clean and the navigation is clear. The homepage headline (â€˜A step toward a skilled lifeâ€™) sets a strong tone, and the â€˜Aboutâ€™ section clearly states the mission and history of the institute. Including a gallery and downloads section adds useful value for visitors.\r\nTo improve further, consider refining a few details: ensure all staff role-cards have matching images and polish any placeholder text, make sure contact information is easy to scan on mobile devices, and check for consistency in fonts and spacing across all pages. Overall very good work â€” with a bit more polish this will be a strong professional site.', 5, 1),
(9, 'Pushpika', 'à¶‰à¶­à·à¶¸à¶­à·Š à·„à·œà¶³à¶§ à¶šà¶»à¶½à· à·€à·’à·à·’à·‚à·Šà¶§ à¶±à·’à¶»à·Šà¶¸à·à¶«à¶ºà¶šà·Š', 5, 1),
(10, 'Kaplla', 'Dddddffff', 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `image_name` varchar(255) DEFAULT 'img_vta',
  `image_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `image_name`, `image_path`, `created_at`) VALUES
(18, 'gg', '../uploads/gallery/690453724d357_1761891186.jpg', '2025-10-31 06:13:06'),
(19, 'A1', '../uploads/gallery/690453868c08b_1761891206.jpg', '2025-10-31 06:13:26'),
(20, 'a2', '../uploads/gallery/6904538fa01b2_1761891215.jpg', '2025-10-31 06:13:35'),
(21, 'a4', '../uploads/gallery/690453c5c4653_1761891269.jpg', '2025-10-31 06:14:29'),
(22, 'A6', '../uploads/gallery/69045cadb5838_1761893549.jpg', '2025-10-31 06:52:29'),
(23, 'a8', '../uploads/gallery/69045ccd136cb_1761893581.jpg', '2025-10-31 06:53:01'),
(24, 'A7', '../uploads/gallery/69045d1ceeaac_1761893660.jpg', '2025-10-31 06:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `mail_settings`
--

CREATE TABLE `mail_settings` (
  `id` int(11) NOT NULL,
  `send_mail` varchar(255) DEFAULT NULL,
  `cc_mail` varchar(255) DEFAULT NULL,
  `bcc_mail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mail_settings`
--

INSERT INTO `mail_settings` (`id`, `send_mail`, `cc_mail`, `bcc_mail`) VALUES
(1, 'earlakshmanvta@gmail.com', 'chamikasandeepa40@gmail.com', 'earlakshmanvtasl@gmail.com');

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

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `course_id`, `module_name`, `module_description`, `order_no`, `created_at`, `updated_at`) VALUES
(1, 7, '	Identify user requirements for software solution', NULL, 'K72T001U01', '2025-10-31 09:44:12', '2025-10-31 09:44:12'),
(2, 7, 'Analyze requirements and design functionality of software solution	', NULL, 'K72T001U02', '2025-10-31 09:44:55', '2025-10-31 09:44:55'),
(3, 7, 'Implement database management system	', NULL, 'K72T001U03', '2025-10-31 09:45:17', '2025-10-31 09:45:17'),
(4, 7, 'Develop and test software	', NULL, 'K72T001U04', '2025-10-31 09:45:40', '2025-10-31 09:45:40'),
(5, 7, 'Create design for print media	', NULL, 'K72T001U05', '2025-10-31 09:45:57', '2025-10-31 09:45:57'),
(6, 7, 'Design and develop multimedia	', NULL, 'K72T001U06', '2025-10-31 09:46:24', '2025-10-31 09:46:24'),
(7, 7, 'Design, develop and host websites', NULL, 'K72T001U07', '2025-10-31 09:46:50', '2025-10-31 09:48:27'),
(8, 7, '	Manage computer networks with security measures and cloud environments', NULL, 'K72T001U08', '2025-10-31 09:48:08', '2025-10-31 09:48:08'),
(9, 7, 'Design and develop mobile app	', NULL, 'K72T001U09', '2025-10-31 09:49:27', '2025-10-31 09:49:27'),
(10, 7, 'Manage workplace information	', NULL, 'EMPU01', '2025-10-31 09:50:11', '2025-10-31 09:50:11'),
(11, 7, 'Manage workplace communication	', NULL, 'EMPU02', '2025-10-31 09:50:51', '2025-10-31 09:50:51'),
(12, 7, 'Plan work to be performed in the workplace	', NULL, 'EMPU03', '2025-10-31 09:51:10', '2025-10-31 09:51:10'),
(13, 8, 'Prepare Bill of Quantities for single/two storey buildings	', NULL, 'F45T002U01', '2025-10-31 10:04:33', '2025-10-31 10:04:33'),
(14, 8, 'Price Bill of Quantities for single/two storey buildings	', NULL, 'F45T002U02', '2025-10-31 10:04:54', '2025-10-31 10:04:54'),
(15, 8, 'Assist to select procurement methods	', NULL, 'F45T002U03', '2025-10-31 10:05:10', '2025-10-31 10:05:10'),
(16, 8, 'Prepare bidding documents	', NULL, 'F45T002U04', '2025-10-31 10:05:47', '2025-10-31 10:05:47'),
(17, 8, 'Prepare bids submittals on behalf of contractors	', NULL, 'F45T002U05', '2025-10-31 10:06:15', '2025-10-31 10:06:15'),
(18, 8, 'Select contractor through bidding procedure	', NULL, 'F45T002U06', '2025-10-31 10:06:35', '2025-10-31 10:06:35'),
(19, 8, 'Prepare preliminary project cost estimates	', NULL, 'F45T002U07', '2025-10-31 10:06:53', '2025-10-31 10:06:53'),
(20, 8, 'Manage project cost	', NULL, 'F45T002U08', '2025-10-31 10:07:20', '2025-10-31 10:07:20'),
(21, 8, 'Manage workplace information	', NULL, 'EMPU01	', '2025-10-31 10:07:39', '2025-10-31 10:07:39'),
(22, 8, 'Manage workplace communication	', NULL, 'EMPU02', '2025-10-31 10:08:29', '2025-10-31 10:08:29'),
(23, 8, 'Plan work to be performed in the workplace	', NULL, 'EMPU03', '2025-10-31 10:08:58', '2025-10-31 10:08:58'),
(24, 9, 'Practice workplace communication and interpersonal relations	', NULL, 'E40S002BU1', '2025-10-31 10:14:48', '2025-10-31 10:14:48'),
(25, 9, 'Apply occupational literacy and numeracy	', NULL, 'E40S002BU2', '2025-10-31 10:15:11', '2025-10-31 10:15:11'),
(26, 9, 'Work in teams	', NULL, 'E40S002BU3', '2025-10-31 10:15:28', '2025-10-31 10:15:28'),
(27, 9, 'Practice occupational health and safety measures	', NULL, 'E40S002BU4', '2025-10-31 10:15:45', '2025-10-31 10:15:45'),
(28, 9, 'Carry out preliminary inspection of electrical appliances	', NULL, 'E40S002U01', '2025-10-31 10:16:06', '2025-10-31 10:16:06'),
(29, 9, 'Prepare estimate for servicing /repairing of electrical appliances	', NULL, 'E40S002U02', '2025-10-31 10:16:48', '2025-10-31 10:16:48'),
(30, 9, 'Service / repair heating elements and accessories of electrical appliances	', NULL, 'E40S002U03', '2025-10-31 10:17:06', '2025-10-31 10:17:06'),
(31, 9, 'Service / repair rotating electrical appliances	', NULL, 'E40S002U04', '2025-10-31 10:17:23', '2025-10-31 10:17:23'),
(32, 9, 'Service/ repair of electrical appliances with electronic control systems	', NULL, 'E40S002U05', '2025-10-31 10:17:41', '2025-10-31 10:17:41'),
(33, 10, 'Develop graphics for print media	', NULL, 'K72S012U01', '2025-10-31 10:22:25', '2025-10-31 10:22:25'),
(34, 10, 'Develop graphics for visual media	', NULL, 'K72S012U02', '2025-10-31 10:22:51', '2025-10-31 10:22:51'),
(35, 10, 'Produce audio/video content	', NULL, 'K72S012U03', '2025-10-31 10:23:09', '2025-10-31 10:23:09'),
(36, 10, 'Develop 2D/3D digital content	', NULL, 'K72S012U04', '2025-10-31 10:23:27', '2025-10-31 10:23:27'),
(37, 10, 'Develop game	', NULL, 'K72S012U05', '2025-10-31 10:23:46', '2025-10-31 10:23:46'),
(38, 10, 'Carryout workplace communication	', NULL, 'K72S012U06', '2025-10-31 10:24:04', '2025-10-31 10:24:04'),
(39, 10, 'Apply occupational literacy and numeracy	', NULL, 'K72S012U07', '2025-10-31 10:24:19', '2025-10-31 10:24:19'),
(40, 10, 'Work in teams	', NULL, 'K72S012U08', '2025-10-31 10:24:34', '2025-10-31 10:24:34'),
(41, 10, 'Practice occupational health and safety measures	', NULL, 'K72S012U09', '2025-10-31 10:24:54', '2025-10-31 10:24:54'),
(42, 10, 'Career skills 2	', NULL, 'K72S012U10', '2025-10-31 10:25:13', '2025-10-31 10:25:13'),
(43, 1, 'Gather application development requirements	', NULL, 'K72S014U01', '2025-10-31 10:30:51', '2025-10-31 21:35:39'),
(44, 1, 'Develop desktop applications	', NULL, 'K72S014U02', '2025-10-31 10:31:13', '2025-10-31 21:35:39'),
(45, 1, 'Develop web applications	', NULL, 'K72S014U03', '2025-10-31 10:31:30', '2025-10-31 21:35:39'),
(46, 1, 'Develop mobile applications	', NULL, 'K72S014U04', '2025-10-31 10:31:45', '2025-10-31 21:35:39'),
(47, 1, 'Practice workplace communication and interpersonal relations	', NULL, 'K72S014U05', '2025-10-31 10:32:01', '2025-10-31 21:35:39'),
(48, 1, 'Apply occupational literacy and numeracy	', NULL, 'K72S014U06', '2025-10-31 10:32:20', '2025-10-31 21:35:39'),
(49, 1, 'Work in teams	', NULL, 'K72S014U07', '2025-10-31 10:32:43', '2025-10-31 21:35:39'),
(50, 1, 'Practice occupational health and safety procedures in a workplace	', NULL, 'K72S014U08', '2025-10-31 10:33:25', '2025-10-31 21:35:39'),
(51, 1, 'Career skills 2	', NULL, 'K72S014U09', '2025-10-31 10:33:42', '2025-10-31 21:35:39'),
(52, 12, 'Practice workplace communication and interpersonal relations	', NULL, 'E40S001BU1', '2025-10-31 10:37:27', '2025-10-31 10:37:27'),
(53, 12, 'Apply occupational literacy and numeracy	', NULL, 'E40S001BU2', '2025-10-31 10:37:47', '2025-10-31 10:37:47'),
(54, 12, 'Work in teams	', NULL, 'E40S001BU3', '2025-10-31 10:38:11', '2025-10-31 10:38:11'),
(55, 12, 'Practice occupational health and safety procedures in a workplace	', NULL, 'E40S001BU4', '2025-10-31 10:38:34', '2025-10-31 10:38:34'),
(56, 12, 'Prepare estimates for electrical installations	', NULL, 'E40S001U01', '2025-10-31 10:38:52', '2025-10-31 10:38:52'),
(57, 12, 'Carry out electrical installations in buildings	', NULL, 'E40S001U02', '2025-10-31 10:39:55', '2025-10-31 10:39:55'),
(58, 12, 'Carry out industrial / commercial electrical installations	', NULL, 'E40S001U03', '2025-10-31 10:40:15', '2025-10-31 10:40:15'),
(59, 12, 'Carry out commissioning, decommissioning and maintaining of industrial electrical installations	', NULL, 'E40S001U04', '2025-10-31 10:41:27', '2025-10-31 10:41:27'),
(60, 12, 'Maintain electric motors, generators and Low Voltage (LV) transformers	', NULL, 'E40S001U05', '2025-10-31 10:41:46', '2025-10-31 10:41:46'),
(61, 12, 'Install and maintain electrical control and protective switchgear systems	', NULL, 'E40S001U06', '2025-10-31 10:42:04', '2025-10-31 10:42:04'),
(62, 12, 'Perform installation and maintenance of Programmable Logic Control (PLC) systems	', NULL, 'E40S001U07', '2025-10-31 10:42:28', '2025-10-31 10:42:28'),
(63, 12, 'Prepare estimate for Internet of Things (IoT)/Smart automation requirements	', NULL, 'E40S001U08', '2025-10-31 10:42:57', '2025-10-31 10:42:57'),
(64, 12, 'Install and maintain Internet of Things (IoT) based/ Smart devices	', NULL, 'E40S001U09', '2025-10-31 10:43:16', '2025-10-31 10:43:16'),
(65, 13, 'Assemble computer components and install software	', NULL, 'K72S015U01', '2025-10-31 10:47:56', '2025-10-31 10:47:56'),
(66, 13, 'Perform IT help desk process	', NULL, 'K72S015U03', '2025-10-31 10:48:15', '2025-10-31 10:48:15'),
(67, 13, 'Perform the basic configuration on Small Office Home Office (SOHO) network	', NULL, 'K72S015U03', '2025-10-31 10:48:35', '2025-10-31 10:48:35'),
(68, 13, 'Apply IT infrastructure security	', NULL, 'K72S015U04', '2025-10-31 10:48:50', '2025-10-31 10:48:50'),
(69, 13, 'Troubleshoot of hardware, software and network	', NULL, 'K72S015U05', '2025-10-31 10:49:04', '2025-10-31 10:49:04'),
(70, 13, 'Perform backup/restore tasks and identify disaster recovery strategies	', NULL, 'K72S015U06', '2025-10-31 10:49:19', '2025-10-31 10:49:19'),
(71, 13, 'Practice workplace communication and interpersonal relations	', NULL, 'K72S015U07', '2025-10-31 10:49:34', '2025-10-31 10:49:34'),
(72, 13, 'Apply occupational literacy and numeracy	', NULL, 'K72S015U08', '2025-10-31 10:49:49', '2025-10-31 10:49:49'),
(73, 13, 'Work in teams	', NULL, 'K72S015U09', '2025-10-31 10:50:09', '2025-10-31 10:50:09'),
(74, 13, 'Practice occupational health and safety procedures in an workplace	', NULL, 'K72S015U10', '2025-10-31 10:50:25', '2025-10-31 10:50:25'),
(75, 13, 'Career skills 2	', NULL, 'K72S015U11', '2025-10-31 10:50:39', '2025-10-31 10:50:39'),
(76, 14, 'Demonstrate ability to work in teams	', NULL, 'M80S001BU1', '2025-10-31 11:00:57', '2025-10-31 11:00:57'),
(77, 14, 'Adopt occupational health and safety procedures in the ECCD Centre	', NULL, 'M80S001BU2', '2025-10-31 11:01:12', '2025-10-31 11:01:12'),
(78, 14, 'Plan learning-teaching activities	', NULL, 'M80S001U01', '2025-10-31 11:01:28', '2025-10-31 11:01:28'),
(79, 14, 'Prepare to organize and implement learning-teaching activities	', NULL, 'M80S001U02', '2025-10-31 11:01:48', '2025-10-31 11:01:48'),
(80, 14, 'Create an appropriate learning-teaching environment	', NULL, 'M80S001U03', '2025-10-31 11:03:23', '2025-10-31 11:03:23'),
(81, 14, 'Retain security and safety of children	', NULL, 'M80S001U04', '2025-10-31 11:03:40', '2025-10-31 11:03:40'),
(82, 14, 'Facilitate the development of language and early literacy skills of children	', NULL, 'M80S001U05', '2025-10-31 11:03:59', '2025-10-31 11:03:59'),
(83, 14, 'Facilitate the development of cognitive skills of children	', NULL, 'M80S001U06', '2025-10-31 11:20:28', '2025-10-31 11:20:28'),
(84, 14, 'Facilitate the development of emotional and social skills of children	', NULL, 'M80S001U07', '2025-10-31 11:21:11', '2025-10-31 11:21:11'),
(85, 14, 'Facilitate the development of health and physical skills of children	', NULL, 'M80S001U08', '2025-10-31 11:21:29', '2025-10-31 11:21:29'),
(86, 14, 'Facilitate the development of aesthetic skills of children	', NULL, 'M80S001U09', '2025-10-31 11:21:47', '2025-10-31 11:21:47'),
(87, 14, 'Care for children\'s health and nutrition	', NULL, 'M80S001U10', '2025-10-31 11:22:03', '2025-10-31 11:22:03'),
(88, 14, 'Attend to children with special needs	', NULL, 'M80S001U11', '2025-10-31 11:22:25', '2025-10-31 11:22:25'),
(89, 14, 'Carryout continuous professional development	', NULL, 'M80S001U12', '2025-10-31 11:22:49', '2025-10-31 11:22:49'),
(90, 15, 'Practice workplace communication and interpersonal relations	', NULL, 'E41S001BU1', '2025-10-31 11:26:47', '2025-10-31 11:26:47'),
(91, 15, 'Apply occupational literacy and numeracy	', NULL, 'E41S001BU2', '2025-10-31 11:27:04', '2025-10-31 11:27:04'),
(92, 15, 'Work in teams	', NULL, 'E41S001BU3', '2025-10-31 11:27:23', '2025-10-31 11:27:23'),
(93, 15, 'Practice occupational health and safety procedures in a workplace	', NULL, 'E41S001BU4', '2025-10-31 11:27:39', '2025-10-31 11:27:39'),
(94, 15, 'Prepare for plumbing installation and maintenance	', NULL, 'E41S001U01', '2025-10-31 11:27:55', '2025-10-31 11:27:55'),
(95, 15, 'Install cold water supply pipelines	', NULL, 'E41S001U02', '2025-10-31 11:28:09', '2025-10-31 11:28:09'),
(96, 15, 'Install hot water supply pipelines	', NULL, 'E41S001U03', '2025-10-31 11:28:25', '2025-10-31 11:28:25'),
(97, 15, 'Install sanitary fixtures and accessories	', NULL, 'E41S001U04', '2025-10-31 11:28:40', '2025-10-31 11:28:40'),
(98, 15, 'Install advanced sanitary fixtures and accessories	', NULL, 'E41S001U05', '2025-10-31 11:28:57', '2025-10-31 11:28:57'),
(99, 15, 'Install Sewerage, Wastewater, Vent (SWV) and storm water systems	', NULL, 'E41S001U06', '2025-10-31 11:29:17', '2025-10-31 11:29:17'),
(100, 15, 'Install metal pipes and fittings for hot and cold-water applications	', NULL, 'E41S001U07', '2025-10-31 11:29:31', '2025-10-31 11:29:31'),
(101, 15, 'Install pumps	', NULL, 'E41S001U08', '2025-10-31 11:29:49', '2025-10-31 11:29:49'),
(102, 15, 'Repair and maintain plumbing system	', NULL, 'E41S001U09', '2025-10-31 11:30:03', '2025-10-31 11:30:03'),
(103, 15, 'Prepare estimates for plumbing installations	', NULL, 'E41S001U10', '2025-10-31 11:30:17', '2025-10-31 11:30:17'),
(104, 16, 'Practice workplace communication and interpersonal relations	', NULL, 'H55S010BU1', '2025-10-31 11:33:35', '2025-10-31 11:33:35'),
(105, 16, 'Apply occupational literacy and numeracy	', NULL, 'H55S010BU2', '2025-10-31 11:33:57', '2025-10-31 11:33:57'),
(106, 16, 'Work in teams	', NULL, 'H55S010BU3', '2025-10-31 11:34:11', '2025-10-31 11:34:11'),
(107, 16, 'Practice occupational health and safety measures	', NULL, 'H55S010BU4', '2025-10-31 11:34:26', '2025-10-31 11:34:26'),
(108, 16, 'Prepare Mise-en Place	', NULL, 'H55S010U01', '2025-10-31 11:34:40', '2025-10-31 11:34:40'),
(109, 16, 'Prepare basic foods and beverages	', NULL, 'H55S010U02', '2025-10-31 11:34:55', '2025-10-31 11:34:55'),
(110, 16, 'Prepare beverages	', NULL, 'H55S010U03', '2025-10-31 11:35:13', '2025-10-31 11:35:13'),
(111, 16, 'Prepare sandwiches, salads, and appetizers	', NULL, 'H55S010U04', '2025-10-31 11:35:28', '2025-10-31 11:35:28'),
(112, 16, 'Prepare stocks, soups and sauces	', NULL, 'H55S010U05', '2025-10-31 11:35:44', '2025-10-31 11:35:44'),
(113, 16, 'Prepare hot range foods	', NULL, 'H55S010U06', '2025-10-31 11:35:58', '2025-10-31 11:35:58'),
(114, 16, 'Prepare desserts	', NULL, 'H55S010U07', '2025-10-31 11:36:23', '2025-10-31 11:36:23'),
(115, 16, 'Open and close down kitchen	', NULL, 'H55S010U08', '2025-10-31 11:36:37', '2025-10-31 11:36:37'),
(116, 17, 'Practice workplace communication and interpersonal relations	', NULL, 'H55S005BU1', '2025-10-31 11:40:40', '2025-10-31 11:40:40'),
(117, 17, 'Apply occupational literacy and numeracy	', NULL, 'H55S005BU2', '2025-10-31 11:40:56', '2025-10-31 11:40:56'),
(118, 17, 'Work in teams	', NULL, 'H55S005BU3', '2025-10-31 11:41:10', '2025-10-31 11:41:10'),
(119, 17, 'Practice occupational health and safety measures	', NULL, 'H55S005BU4', '2025-10-31 11:41:32', '2025-10-31 11:41:32'),
(120, 17, 'Open, arrange and close restaurant	', NULL, 'H55S005U01', '2025-10-31 11:41:49', '2025-10-31 11:41:49'),
(121, 17, 'Provide table service	', NULL, 'H55S005U02', '2025-10-31 11:42:06', '2025-10-31 11:42:06'),
(122, 17, 'Provide wine service	', NULL, 'H55S005U03', '2025-10-31 11:42:25', '2025-10-31 11:42:25'),
(123, 17, 'Provide In-Room Dining (IRD) services	', NULL, 'H55S005U04', '2025-10-31 11:42:42', '2025-10-31 11:42:42'),
(124, 17, 'Maintain personal grooming & hygiene	', NULL, 'H55S005U05', '2025-10-31 11:42:58', '2025-10-31 11:42:58'),
(125, 17, 'Serve beverages	', NULL, 'H55S005U06', '2025-10-31 11:43:18', '2025-10-31 11:43:18'),
(126, 17, 'Carryout banquet operations	', NULL, 'H55S005U07', '2025-10-31 11:43:38', '2025-10-31 11:43:38'),
(127, 17, 'Provide Out-Door catering services	', NULL, 'H55S005U08', '2025-10-31 11:43:54', '2025-10-31 11:43:54'),
(128, 18, 'Practice workplace communication and interpersonal relations	', NULL, 'K72S004BU1', '2025-10-31 11:53:20', '2025-10-31 11:53:20'),
(129, 18, 'Apply occupational literacy and numeracy	', NULL, 'K72S004BU2', '2025-10-31 11:53:42', '2025-10-31 11:53:42'),
(130, 18, 'Work in a team	', NULL, 'K72S004BU3', '2025-10-31 11:53:57', '2025-10-31 11:53:57'),
(131, 18, 'Practice occupational health and safety measures	', NULL, 'K72S004BU4', '2025-10-31 11:54:11', '2025-10-31 11:54:11'),
(132, 18, 'Use the computer and manage files within standard operating systems	', NULL, 'K72S004U01', '2025-10-31 11:54:33', '2025-10-31 11:54:33'),
(133, 18, 'Perform word processing	', NULL, 'K72S004U02', '2025-10-31 11:54:51', '2025-10-31 11:54:51'),
(134, 18, 'Prepare spreadsheets	', NULL, 'K72S004U03', '2025-10-31 11:55:15', '2025-10-31 11:55:15'),
(135, 18, 'Prepare presentation resources	', NULL, 'K72S004U04', '2025-10-31 11:55:30', '2025-10-31 11:55:30'),
(136, 18, 'Perform basic operations of cloud computing including internet and email services	', NULL, 'K72S004U05', '2025-10-31 11:55:48', '2025-10-31 11:55:48'),
(137, 18, 'Handle relational database	', NULL, 'K72S004U06', '2025-10-31 11:56:04', '2025-10-31 11:56:04'),
(138, 18, 'Conduct routine maintenance services of computer system and peripherals	', NULL, 'K72S004U07', '2025-10-31 11:56:19', '2025-10-31 11:56:19'),
(139, 18, 'Produce print page layout and multimedia objects	', NULL, 'K72S004U08', '2025-10-31 11:56:37', '2025-10-31 11:56:37'),
(140, 18, 'Analyze, design and develop information system	', NULL, 'K72S004U09', '2025-10-31 11:56:55', '2025-10-31 11:56:55'),
(141, 18, 'Handle relational databases using standard SQL	', NULL, 'K72S004U10', '2025-10-31 11:57:17', '2025-10-31 11:57:17'),
(142, 18, 'Design static website	', NULL, 'K72S004U11', '2025-10-31 11:57:31', '2025-10-31 11:57:31'),
(143, 19, 'Maintain housekeeping standards	', NULL, 'N85S024U01', '2025-10-31 12:00:52', '2025-10-31 12:00:52'),
(144, 19, 'Arrange physical environment	', NULL, 'N85S024U02', '2025-10-31 12:01:44', '2025-10-31 12:01:44'),
(145, 19, 'Maintain health, safety and effective environment	', NULL, 'N85S024U03', '2025-10-31 12:02:00', '2025-10-31 12:02:00'),
(146, 19, 'Provide food and beverages	', NULL, 'N85S024U04', '2025-10-31 12:02:15', '2025-10-31 12:02:15'),
(147, 19, 'Provide physical care	', NULL, 'N85S024U05', '2025-10-31 12:02:32', '2025-10-31 12:02:32'),
(148, 19, 'Foster holistic development of children	', NULL, 'N85S024U06', '2025-10-31 12:02:51', '2025-10-31 12:02:51'),
(149, 19, 'Maintain professionalism	', NULL, 'N85S024U07', '2025-10-31 12:03:06', '2025-10-31 12:03:06'),
(150, 19, 'Care of a child with special needs	', NULL, 'N85S024U08', '2025-10-31 12:03:22', '2025-10-31 12:03:22'),
(151, 19, 'Administer medications and provide first aids	', NULL, 'N85S024U09', '2025-10-31 12:03:39', '2025-10-31 12:03:39'),
(153, 19, 'Carry out child care centre administration activities	', NULL, 'N85S024U10', '2025-10-31 12:17:29', '2025-10-31 12:17:29'),
(154, 19, 'Practice occupational health and safety measures	', NULL, 'N85S024U11', '2025-10-31 12:17:51', '2025-10-31 12:17:51'),
(155, 19, 'Practice occupational health and safety measures	', NULL, 'N85S024U11', '2025-10-31 12:18:10', '2025-10-31 12:18:10'),
(156, 19, 'Carryout workplace communication	', NULL, 'N85S024U12', '2025-10-31 12:18:26', '2025-10-31 12:18:26'),
(157, 19, 'Apply workplace literacy and numeracy	', NULL, 'N85S024U13', '2025-10-31 12:18:44', '2025-10-31 12:18:44'),
(158, 19, 'Work in teams	', NULL, 'N85S024U14', '2025-10-31 12:19:04', '2025-10-31 12:19:04'),
(159, 19, 'Career skill 1	', NULL, 'N85S024U15', '2025-10-31 12:19:21', '2025-10-31 12:19:21'),
(160, 19, 'Career skill 2	', NULL, 'N85S024U16', '2025-10-31 12:19:38', '2025-10-31 12:19:38'),
(161, 20, 'Apply occupational literacy and numeracy	', NULL, 'E40S003BU2', '2025-10-31 12:32:10', '2025-10-31 12:32:10'),
(162, 20, 'Practice workplace communication and interpersonal relations	', NULL, 'E40S003BU1', '2025-10-31 12:33:17', '2025-10-31 12:33:17'),
(163, 20, 'Work in teams	', NULL, 'E40S003BU3', '2025-10-31 12:33:34', '2025-10-31 12:33:34'),
(164, 20, 'Practice occupational health and safety measures	', NULL, 'E40S003BU4', '2025-10-31 12:33:52', '2025-10-31 12:33:52'),
(165, 20, 'Perform receiving and handing over electrical machines	', NULL, 'E40S003U01', '2025-10-31 12:34:08', '2025-10-31 12:34:08'),
(166, 20, 'Test and diagnose faults in electrical machines	', NULL, 'E40S003U02', '2025-10-31 12:34:30', '2025-10-31 12:34:30'),
(167, 20, 'Rewind electric motors & alternators	', NULL, 'E40S003U03', '2025-10-31 12:34:45', '2025-10-31 12:34:45'),
(168, 20, 'Rewind transformers & coils	', NULL, 'E40S003U04', '2025-10-31 12:35:02', '2025-10-31 12:35:02'),
(169, 20, 'Prepare estimates for re-winding, repairing and servicing of electrical machines	', NULL, 'E40S003U05', '2025-10-31 12:35:17', '2025-10-31 12:35:17'),
(170, 21, 'Practice workplace communication and interpersonal relations	', NULL, 'F45S003BU1', '2025-10-31 12:40:26', '2025-10-31 12:40:26'),
(171, 21, 'Apply occupational numeracy	', NULL, 'F45S003BU2', '2025-10-31 12:41:10', '2025-10-31 12:41:10'),
(172, 21, 'Work in teams	', NULL, 'F45S003BU3', '2025-10-31 12:41:29', '2025-10-31 12:41:29'),
(173, 21, 'Practice occupational health and safety procedures in a workplace	', NULL, 'F45S003BU4', '2025-10-31 12:41:42', '2025-10-31 12:41:42'),
(174, 21, 'Fabricate & install showcases	', NULL, 'F45S003U01', '2025-10-31 12:41:57', '2025-10-31 12:41:57'),
(175, 21, 'Fabricate & install shower cubicles	', NULL, 'F45S003U02', '2025-10-31 12:42:16', '2025-10-31 12:42:16'),
(176, 21, 'Fabricate & install ceilings	', NULL, 'F45S003U03', '2025-10-31 12:42:39', '2025-10-31 12:42:39'),
(177, 21, 'Fabricate & install miscellaneous aluminum items	', NULL, 'F45S003U04', '2025-10-31 12:42:54', '2025-10-31 12:42:54'),
(178, 21, 'Fabricate & install windows	', NULL, 'F45S003U05', '2025-10-31 12:43:09', '2025-10-31 12:43:09'),
(179, 21, 'Fabricate & install doors	', NULL, 'F45S003U06', '2025-10-31 12:43:27', '2025-10-31 12:43:27'),
(180, 21, 'Fabricate & install partitions and shop fronts	', NULL, 'F45S003U07', '2025-10-31 12:44:04', '2025-10-31 12:44:04'),
(181, 21, 'Fabricate & install pantry cupboard	', NULL, 'F45S003U08', '2025-10-31 12:44:21', '2025-10-31 12:44:21'),
(182, 21, 'Fabricate & install aluminum composite panels	', NULL, 'F45S003U09', '2025-10-31 12:44:36', '2025-10-31 12:44:36'),
(183, 21, 'Fabricate ladders	', NULL, 'F45S003U10', '2025-10-31 12:44:53', '2025-10-31 12:44:53'),
(184, 21, 'Fabricate & install curtain walls	', NULL, 'F45S003U11', '2025-10-31 12:45:09', '2025-10-31 12:45:09'),
(185, 21, 'Install tempered fix glass partition and glass door	', NULL, 'F45S003U12', '2025-10-31 12:45:24', '2025-10-31 12:45:24'),
(186, 21, 'Fabricate & install roller shutter/doors	', NULL, 'F45S003U13', '2025-10-31 12:45:39', '2025-10-31 12:45:39'),
(187, 21, 'Prepare cost estimate	', NULL, 'F45S003U14', '2025-10-31 12:45:54', '2025-10-31 12:45:54'),
(188, 22, 'Practice workplace communication and interpersonal relations	', NULL, 'D15S002BU1', '2025-10-31 12:52:04', '2025-10-31 12:52:04'),
(189, 22, 'Apply occupational literacy and numeracy	', NULL, 'D15S002BU2', '2025-10-31 12:52:21', '2025-10-31 12:52:21'),
(190, 22, 'Work in teams	', NULL, 'D15S002BU3', '2025-10-31 12:52:36', '2025-10-31 12:52:36'),
(191, 22, 'Practice occupational health and safety measures	', NULL, 'D15S002BU4', '2025-10-31 12:52:52', '2025-10-31 12:52:52'),
(192, 22, 'Mise en place	', NULL, 'D15S002U01', '2025-10-31 12:53:06', '2025-10-31 12:53:06'),
(193, 22, 'Produce bread & buns	', NULL, 'D15S002U02', '2025-10-31 12:53:20', '2025-10-31 12:53:20'),
(194, 22, 'Produce pastries	', NULL, 'D15S002U03', '2025-10-31 12:53:35', '2025-10-31 12:53:35'),
(195, 22, 'Prepare desserts	', NULL, 'D15S002U04', '2025-10-31 12:53:50', '2025-10-31 12:53:50'),
(196, 22, 'Produce cakes and cookies	', NULL, 'D15S002U05', '2025-10-31 12:54:08', '2025-10-31 12:54:08'),
(197, 22, 'Handle routine function of a pastry and bakery	', NULL, 'D15S002U06', '2025-10-31 12:54:25', '2025-10-31 12:54:25'),
(198, 23, 'Practice workplace communication and interpersonal relations	', NULL, 'F45S007BU1', '2025-10-31 13:00:51', '2025-10-31 13:00:51'),
(199, 23, 'Apply occupational literacy and numeracy	', NULL, 'F45S007BU2', '2025-10-31 13:01:06', '2025-10-31 13:01:06'),
(200, 23, 'Work in teams	', NULL, 'F45S007BU3', '2025-10-31 13:01:21', '2025-10-31 13:01:21'),
(201, 23, 'Practice occupational health and safety procedures in a workplace	', NULL, 'F45S007BU4', '2025-10-31 13:01:36', '2025-10-31 13:01:36'),
(202, 23, 'Assist to prepare Bill of Quantities (BOQ)	', NULL, 'F45S007U01', '2025-10-31 13:02:16', '2025-10-31 13:02:16'),
(203, 23, 'Assist in estimating and tendering/bidding process	', NULL, 'F45S007U02', '2025-10-31 13:03:12', '2025-10-31 13:03:12'),
(204, 23, 'Assist to quantity surveying activities during post contract stage	', NULL, 'F45S007U03', '2025-10-31 13:03:33', '2025-10-31 13:03:33'),
(205, 24, 'Practice workplace communication and interpersonal relations	', NULL, 'N85S010BU1', '2025-10-31 13:20:36', '2025-10-31 13:20:36'),
(206, 24, 'Apply occupational literacy and numeracy	', NULL, 'N85S010BU2', '2025-10-31 13:21:46', '2025-10-31 13:21:46'),
(207, 24, 'Work in teams	', NULL, 'N85S010BU3', '2025-10-31 13:22:01', '2025-10-31 13:22:01'),
(208, 24, 'Practice health and safety measures	', NULL, 'N85S010BU4', '2025-10-31 13:22:15', '2025-10-31 13:22:15'),
(209, 24, 'Assess the client and prepare care plan	', NULL, 'N85S010U01', '2025-10-31 13:22:29', '2025-10-31 13:22:29'),
(210, 24, 'Perform self-preparation activities	', NULL, 'N85S010U02', '2025-10-31 13:22:44', '2025-10-31 13:22:44'),
(211, 24, 'Maintain the relationship with the client	', NULL, 'N85S010U03', '2025-10-31 13:22:59', '2025-10-31 13:22:59'),
(212, 24, 'Attend domestic affairs of the client	', NULL, 'N85S010U04', '2025-10-31 13:23:13', '2025-10-31 13:23:13'),
(213, 24, 'Prepare living area of the client	', NULL, 'N85S010U05', '2025-10-31 13:23:30', '2025-10-31 13:23:30'),
(214, 24, 'Maintain personal hygiene and appearance of the client	', NULL, 'N85S010U06', '2025-10-31 13:23:44', '2025-10-31 13:23:44'),
(215, 24, 'Maintain nutrition of the client	', NULL, 'N85S010U07', '2025-10-31 13:23:59', '2025-10-31 13:23:59'),
(216, 24, 'Handle clientï¿½s movements	', NULL, 'N85S010U08', '2025-10-31 13:24:17', '2025-10-31 13:24:17'),
(217, 24, 'Provide drug treatment	', NULL, 'N85S010U09', '2025-10-31 13:24:32', '2025-10-31 13:24:32'),
(218, 24, 'Provide special care	', NULL, 'N85S010U10', '2025-10-31 13:24:50', '2025-10-31 13:24:50'),
(219, 24, 'Check, record and maintain vital signs	', NULL, 'N85S010U11', '2025-10-31 13:25:04', '2025-10-31 13:25:04'),
(220, 24, 'Care of an elderly person	', NULL, 'N85S010U12', '2025-10-31 13:25:18', '2025-10-31 13:25:18'),
(221, 24, 'Care of postnatal mother & new born	', NULL, 'N85S010U13', '2025-10-31 13:25:32', '2025-10-31 13:25:32'),
(222, 24, 'Care of an infant/ toddler	', NULL, 'N85S010U14', '2025-10-31 13:25:51', '2025-10-31 13:25:51'),
(223, 24, 'Care of the child	', NULL, 'N85S010U15', '2025-10-31 13:26:06', '2025-10-31 13:26:06'),
(224, 24, 'Care of a client with special needs	', NULL, 'N85S010U16', '2025-10-31 13:26:20', '2025-10-31 13:26:20'),
(225, 25, 'Practice workplace communication and interpersonal relations	', NULL, 'H55S043BU1', '2025-10-31 13:29:30', '2025-10-31 13:29:30'),
(226, 25, 'Apply occupational literacy and numeracy	', NULL, 'H55S043BU2', '2025-10-31 13:30:15', '2025-10-31 13:30:15'),
(227, 25, 'Work in teams	', NULL, 'H55S043BU3', '2025-10-31 13:30:29', '2025-10-31 13:30:29'),
(228, 25, 'Practice occupational health and safety procedures in a workplace	', NULL, 'H55S043BU4', '2025-10-31 13:30:46', '2025-10-31 13:30:46'),
(229, 25, 'Welcome and greet customers	', NULL, 'H55S043U01', '2025-10-31 13:31:00', '2025-10-31 13:31:00'),
(230, 25, 'Handle transaction	', NULL, 'H55S043U02', '2025-10-31 13:31:14', '2025-10-31 13:31:14'),
(231, 25, 'Build and Maintain rapport with customers	', NULL, 'H55S043U03', '2025-10-31 13:31:27', '2025-10-31 13:31:27'),
(232, 25, 'Bid Farewell to Customers	', NULL, 'H55S043U04', '2025-10-31 13:31:42', '2025-10-31 13:31:42'),
(233, 25, 'Carry out housekeeping services	', NULL, 'H55S043U05', '2025-10-31 13:32:02', '2025-10-31 13:32:02'),
(234, 25, 'Carry out food and beverage services	', NULL, 'H55S043U06', '2025-10-31 13:32:15', '2025-10-31 13:32:15'),
(235, 25, 'Carry out kitchen activities	', NULL, 'H55S043U07', '2025-10-31 13:32:28', '2025-10-31 13:32:28');

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
  `type` varchar(50) DEFAULT NULL,
  `course_no` varchar(20) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `login_status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `staff_id`, `service_id`, `first_name`, `last_name`, `nic`, `contact_no`, `email`, `gender`, `password`, `position`, `type`, `course_no`, `profile_photo`, `status`, `login_status`) VALUES
(12, 'NVTI-2025-1261', '1055', 'N.R.', 'Senivirathna', '713222895V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$SW/TgZe.hm3dJWjlxJCLTO8zFbjvmFAK7W74L20M9Z11SkDePTTqS', 'Training Officer', '', NULL, 'NVTI-STAFF-1761925802.jpeg', 'active', 0),
(13, 'NVTI-2025-2827', '1119', 'EAR', 'Lakshman', '793161689v', '0718249323', 'earlakshmanvta@gmail.com', 'Male', '$2y$10$GJYpVkFr65YPnnpPvzCiFOZSzmvkte61/1UUtWQDnLCfqMNIqEOv6', 'Instructor', 'admin', 'K72S014.0', 'NVTI-STAFF-1761886421.jpg', 'active', 1),
(14, 'NVTI-2025-7516', '1048', 'I.U.', 'Wickramanayaka', '197458402410', '0713202751', 'example@gmail.com', 'Female', '$2y$10$hrotYPh7u5IleDMh7TjK7ucL3AeGOo7p4IKbhpn6k6c61PIf5dSpe', 'Assistant Director', 'admin', NULL, 'NVTI-STAFF-1761920605.jpg', 'active', 0),
(15, 'NVTI-2025-5616', '1873', 'G.G.N.S.', 'Wijethilaka', '860663716V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$vldCjQr0QSWDvMZeolPI1.viQQqsdtnz5jmWjam4nrF2bZlzzcAcC', 'Management Assistant', NULL, NULL, 'NVTI-STAFF-1761924431.jpg', 'active', 0),
(16, 'NVTI-2025-3348', '959', 'J.P.T.', 'Nishanthi', '755643173V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$7thYlm7hKn95rYO.cb7ENehiY3kBIl.pWHC5wH1fYnwLR9GZbpWXq', 'Management Assistant', NULL, NULL, NULL, 'active', 0),
(17, 'NVTI-2025-5886', '978', 'H.K.', 'Shiyani', '686210715V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$sYgJWtlimFz5XKde3XLmkuUhelaK8AmRzuS1o7Hl4ZzoRvAAac9FW', 'Program Officer', 'admin', NULL, 'NVTI-STAFF-1761922495.jpg', 'active', 0),
(18, 'NVTI-2025-8949', '1210', 'M.M.K.P.', 'Mayadunne', '756983040V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$rlC5znjPXu8goz4uiNGc7uQtlLE.ChuXR3rkDo/YpWXZmjP0X6H7i', 'Program Officer', 'admin', NULL, 'NVTI-STAFF-1761923552.jpg', 'active', 0),
(19, 'NVTI-2025-4981', '986', 'H.N.', 'Priyanga', '197154700659', '0771111111', 'example@gmail.com', 'Female', '$2y$10$iltEOj0K6G3CEiF9eXZNN.fpVaiVE5lbECphQsm/oBAzFrCw6973u', 'Program Officer', 'admin', NULL, 'NVTI-STAFF-1761922826.jpg', 'active', 0),
(20, 'NVTI-2025-9878', '762', 'D.H.K.', 'Hettiarachchi', '770201071V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$TjSSYkK9fvPjNA2yo7V6zeB5aGgOJyIl9/1qwyTWUKleSilhbDoNu', 'Finance Officer', 'admin', NULL, 'NVTI-STAFF-1761922226.jpg', 'active', 0),
(21, 'NVTI-2025-4569', '2082', 'N.S.A.', 'Arachchi', '895972410V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$CB8BQXev9AG020bycqrSROSUdC2y2Po9TcbtKeVu3EnmLi24sSxba', 'Management Assistant', NULL, NULL, NULL, 'active', 0),
(22, 'NVTI-2025-7531', '2647', 'K.M.G.G.', 'Kanchana', '886664079V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$VgqqP/NrAiknPnrYHTDHbuT64qn6NDQikU2BmLpwTAO4NOQkgjf.i', 'Management Assistant', NULL, NULL, NULL, 'active', 0),
(23, 'NVTI-2025-4034', '987', 'B.M.N.', 'Samanthi', '197486202101', '0771111111', 'example@gmail.com', 'Female', '$2y$10$.9UcUIlV1pnS7kP8p85BUuRAO6ISSbpFk1EgA7guQffG.RLs.n6VG', 'Program Officer', 'admin', NULL, 'NVTI-STAFF-1761921894.jpg', 'active', 0),
(24, 'NVTI-2025-9513', '776', 'K.H.B.S.', 'Jayaseraka', '197304103807', '0771111111', 'example@gmail.com', 'Male', '$2y$10$fNcjvS0jXuLfTpb5g83.Ce4exqjFTv9pwgukpbAHmgS0DrLPsC7sW', 'Management Assistant', NULL, NULL, NULL, 'active', 0),
(25, 'NVTI-2025-8620', '430', 'L.L.', 'Priyanka', '695531532V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$j1dutmVKSGAUmt4AnYpfKOuho/86xIOZ1uzAJi1i2xGwIUeTQTSJq', 'Instructor', '', 'E40S001.6', 'NVTI-STAFF-1761924308.jpg', 'active', 0),
(26, 'NVTI-2025-5953', '2580', 'T.M.', 'Sooriarachchi', '863082536V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$jBLPMCvJEn8YeMV75CH.S.DAxdFXZe68v6T9SFmg52E9uZ3NV2Oy2', 'Instructor', '', 'K72S015.0', 'NVTI-STAFF-1761921986.jpg', 'active', 0),
(27, 'NVTI-2025-5615', '1319', 'H.G.', 'Susantha', '197032800579', '0771111111', 'example@gmail.com', 'Male', '$2y$10$fz/IBkx.k0DX/cnqhQjNk.ruyPQS09Hk9n4knMUtf0gZH5pxnniOi', 'Instructor', '', 'E40S002.3', 'NVTI-STAFF-1761924233.jpg', 'active', 0),
(28, 'NVTI-2025-6401', '1039', 'M.W.S.', 'Prasadika', '745533868V', '0771111111', 'exampl@gmail.com', 'Female', '$2y$10$tuRrhx0DhJu6bFbqiSXGF.AMl5VSU6HUfx/UVIZCO/cTLMeooDeAy', 'Instructor', NULL, 'K72S004.2', 'NVTI-STAFF-1761921664.jpg', 'active', 0),
(29, 'NVTI-2025-2245', '1973', 'R.N.', 'Rillagoda', '807843869V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$T/0zUKOGqavupal4VmaCGOmQ.KnMXei/.HMBMHa.eWY7E9n8coM1a', 'Instructor', '', 'K72S004.2', 'NVTI-STAFF-1761921801.jpg', 'active', 0),
(30, 'NVTI-2025-9238', '2425', 'P.I.', 'Jagoda', '752513287V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$HngBe5KNVSbJmUrrirt1NuPUbzDT/lGcBX9jwhE40Sqoetz20g.o2', 'Instructor', '', 'K72S012.0', 'NVTI-STAFF-1761923483.jpg', 'active', 0),
(31, 'NVTI-2025-7147', '2597', 'K.M.G.D.', 'Sanjeewani', '916232055V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$AITKo0kdLVrBIWn/75bNfODz3Yp1ms7I6HfJbBfa8msFfW9AKQFE.', 'Instructor', '', 'F45T002.1', 'NVTI-STAFF-1761924908.jpg', 'active', 0),
(32, 'NVTI-2025-5642', '2107', 'N.W.S.', 'Erangika', '835553876V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$V4SIIDtDZuwOUb/Oh4Bq2exLhgIaOQgF9I.m6vT1liS0h8JMEdu16', 'Senior Instructor', NULL, 'K72T001.2', 'NVTI-STAFF-1761921726.jpg', 'active', 0),
(33, 'NVTI-2025-6379', '1921', 'U.G.P.K.', 'Nishantha', '762410532V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$7txoTazFz9mkAiE869Qp5e9qLHXzQNAm1Pwx.77j6J5iWnHcvg0xa', 'Instructor', '', 'K72T001.2', 'NVTI-STAFF-1761924337.jpg', 'active', 0),
(34, 'NVTI-2025-8418', '7727', 'R.M.', 'Lakshman', '196511302002', '0771111111', 'example@gmail.com', 'Male', '$2y$10$2otf8jEN/BVtAao15rZNkO2uMvaJxeaPhdKK5.waob9HWgwNIk45e', 'Instructor', '', 'H55S005.1', 'NVTI-STAFF-1761921766.jpg', 'active', 0),
(35, 'NVTI-2025-6221', '1948', 'E.M.P.C.S.', 'Ekanayaka', '198013302145', '0771111111', 'example@gmail.com', 'Male', '$2y$10$6kuOTcfL3ua./6bM6QeU0OAQZHAtSwbPrOcLg482.4xvpRHjxoil6', 'Instructor', '', 'E41S001.5', 'NVTI-STAFF-1761924549.jpg', 'active', 0),
(36, 'NVTI-2025-8934', '1998', 'D.K.D.A.', 'Kumari', '199885910498', '0771111111', 'example@gmail.com', 'Female', '$2y$10$BR09znO.qmbdvZvigvtFP.Z/zm.0TeN4TmWJL6faN7D/DyD4dd40i', 'Instructor', '', 'M80S001.3', 'NVTI-STAFF-1761922687.jpg', 'active', 0),
(37, 'NVTI-2025-8424', '1721', 'W.J.', 'De Silva', '765080479V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$lMKyv74fTDuvMSdgbKl0EOaG2tGEvVonME7a0/wU.Tx/QzpM56i4q', 'Instructor', '', 'H55S010.2', 'NVTI-STAFF-1761922102.jpg', 'active', 0),
(38, 'NVTI-2025-6427', '1444', 'R.C.N.', 'Perera', '197380701751', '0771111111', 'example@gmail.com', 'Female', '$2y$10$pj.0RbV/1RD.7l3jOB6s4.M.0wcS2FPdlrmPsnmSKIxGED.N3f0NK', 'Instructor', '', 'H55S010.2', NULL, 'active', 0),
(39, 'NVTI-2025-4222', '8935', 'A.H.N.', 'Pushpakumari', '925787153V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$VgVVG6CxmTuDDDJ0waSOK.YNdhw5TpOOxmwnSs8szcQ0Etd1xO3VG', 'Instructor', '', 'N85S024.0', 'NVTI-STAFF-1761924989.jpg', 'active', 0),
(40, 'NVTI-2025-1223', '2518', 'S.G.D.', 'Sandamali', '888132848V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$QodDORg9sbBWdXzFuvMBR.AZbLsDni5KEwuPXbXl1WqFlh2AcpR4q', 'Instructor', '', 'E40S003.2', 'NVTI-STAFF-1761921838.jpg', 'active', 0),
(41, 'NVTI-2025-8769', '2245', 'A.G.Y.', 'Shyamali', '747672857V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$PCdewseOYgFdZvKXk7/Z1O/957p9sagp3Jsfa74h4pSoKTBUoqKCu', 'Management Assistant', NULL, NULL, 'NVTI-STAFF-1761924414.jpg', 'active', 0),
(42, 'NVTI-2025-8197', '2302', 'O.M.', 'Chathuranga', '881970414V', '0771111111', 'example@gmail.com', 'Male', '$2y$10$g2tNdMCYzssSUaQbz.7InuTDjP.TGkwyrbQ59z7SIgiCKsreSMJZ2', 'Driver', NULL, NULL, NULL, 'active', 0),
(43, 'NVTI-2025-8142', '8400', 'K.G.P.', 'Chathurika', '915750648V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$h2iCjF7HKEh4QA8d6HmL4udIynW/wZv/msPkx9Ezsti69yzIGOj4K', 'Instructor', '', 'F45S003.4', 'NVTI-STAFF-1761922406.jpg', 'active', 0),
(44, 'NVTI-2025-2488', '1837', 'D.P.', 'Nanayakkara', '695490180V', '0771111111', 'example@gmail.com', 'Female', '$2y$10$auWVfAcvEMqU3yzErUKuoOfXpBWFMv/2YPgXH1cNTF1L6dWjY2tLe', 'Instructor', NULL, 'D15S002.2', 'NVTI-STAFF-1761922029.jpg', 'active', 0),
(45, 'NVTI-2025-8613', '1211', 'K.G.D.S.', 'Gamage', '199934611183', '0771111111', 'example@gmail.com', 'Male', '$2y$10$zT1rJc/kB8WM9Gf2zbvuQuBe50v5qWdZPPb5wYsn/vxkaDLu4eF8K', 'Instructor', '', 'D15S002.2', 'NVTI-STAFF-1762055192.jpg', 'active', 1);

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
(14, 'VTA_BAD965977', 'ravindra', '793161689v', 'uiiuy', '2025-10-01', '0718249323', '0718249323', 'Yes', 'A', 'B', 'B', '', 'Information & Communication Technology', 'Multimedia Designing Associate', '2025-10-31 05:01:15', 0),
(15, 'VTA_BAD585394', 'Katelyn Beasley', '200400912444', 'Sed Nam proident ut', '2003-05-15', '0782509946', '0782509946', 'No', '', '', '', '', 'National Certificate - Software Developer', 'National Diploma - Quantity Surveying', '2025-10-31 06:46:33', 0),
(16, 'VTA_BAD517810', 'Cailin Cunningham', '200300912443', 'Illum architecto no', '1999-10-13', '0782509946', '0782509946', 'No', '', '', '', '', 'National Diploma - Information & Communication Technology', 'National Certificate - Software Developer', '2025-10-31 06:47:00', 0),
(17, 'VTA_BAD946462', 'Ravindra', '793161689v', 'See', '2025-10-20', '0718249323', '0718249323', 'No', '', '', '', '', 'National Certificate - Software Developer', 'National Certificate - Computer Hardware & Network Technician', '2025-10-31 11:19:23', 0),
(18, 'VTA_BAD326467', 'Pushpika', '793161689V', 'hhh', '2025-10-13', '0718249323', '0718249323', 'Yes', 'A', 'A', 'B', '', 'National Certificate - Software Developer', '', '2025-10-31 16:16:05', 0),
(19, 'VTA_BAD532244', 'Test 1', '200625103437', 'asdada', '2025-10-28', '0704071099', '0704071099', 'Yes', 'A', 'A', 'B', 'commerce', 'National Certificate - Multimedia Designing Associate', 'National Certificate - Software Developer', '2025-11-01 11:31:51', 0),
(21, 'VTA_BAD595843', 'TEst 2', '200625103437', 'adsdasd', '2025-10-27', '0704071099', '0704071099', 'Yes', 'A', 'A', 'B', 'commerce', 'National Certificate - Software Developer', 'National Certificate - Software Developer', '2025-11-01 11:43:40', 0),
(22, 'VTA_BAD721942', 'Ravindra 2', '793161689v', 'Ggg', '2025-11-13', '0718249323', '0718249323', 'Yes', 'S', 'F', 'S', 'tech', 'National Certificate - Software Developer', 'National Diploma - Information & Communication Technology', '2025-11-01 13:21:21', 0),
(23, 'VTA_BAD900462', 'Ravindra 2', '793161689v', 'à·†à·Šà·†à·Šà·†à·Šà·†à·Š', '2025-11-19', '0718249323', '0718249323', 'No', '', '', '', '', 'National Certificate - Software Developer', 'National Diploma - Quantity Surveying', '2025-11-01 16:27:16', 0);

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
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mail_settings`
--
ALTER TABLE `mail_settings`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `course_categories`
--
ALTER TABLE `course_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `mail_settings`
--
ALTER TABLE `mail_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `student_enrollments`
--
ALTER TABLE `student_enrollments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
