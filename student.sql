-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: 24 ส.ค. 2023 เมื่อ 03:48 PM
-- เวอร์ชันของเซิร์ฟเวอร์: 8.0.34-0ubuntu0.20.04.1
-- PHP Version: 7.4.3-4ubuntu2.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wordpress_edu29`
--

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `wp_schools`
--

CREATE TABLE `wp_schools` (
  `ID` bigint UNSIGNED NOT NULL,
  `school_id` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'รหัสโรงเรียน10หลัก',
  `school_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'ชื่อย่อ',
  `school_name` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'ชื่อโรงเรียน',
  `go_id` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'รหัสหน่วยงานต้นสังกัด',
  `go_name` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '' COMMENT 'ชื่อหน่วยงานต้นสังกัด',
  `school_status` int DEFAULT '0' COMMENT 'สถานะ',
  `city` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'อำเภอ',
  `province` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'จังหวัด',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- dump ตาราง `wp_schools`
--

INSERT INTO `wp_schools` (`ID`, `school_id`, `school_nicename`, `school_name`, `go_id`, `go_name`, `school_status`, `city`, `province`, `display_name`) VALUES
(1, '3041200101', 'ร.ร.ท.1', 'ร.ร.เทศบาล 1 โพศรี', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(2, '3041200102', 'ร.ร.ท.2', 'ร.ร.เทศบาล 2 มุขมนตรี', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(3, '3041200103', 'ร.ร.ท.3', 'ร.ร.เทศบาล 3 บ้านเหล่า', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(4, '3041200104', 'ร.ร.ท.4', 'ร.ร.เทศบาล 4 วัดโพธิวราราม', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(5, '3041200105', 'ร.ร.ท.5', 'ร.ร.เทศบาล 5 สีหรักษ์วิทยา', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(6, '3041200106', 'ร.ร.ท.6', 'ร.ร.มัธยมเทศบาล 6 นครอุดรธานี', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(7, '3041200107', 'ร.ร.ท.7', 'ร.ร.เทศบาล 7 รถไฟสงเคราะห์', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(8, '3041200108', 'ร.ร.ท.8', 'ร.ร.ไทยรัฐวิทยา 72 (เทศบาล 8)', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(9, '3041200109', 'ร.ร.ท.9', 'ร.ร.เทศบาล 9 มณเฑียรทองอนุสรณ์', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(10, '3041200110', 'ร.ร.ท.10', 'ร.ร.เทศบาล 10 อนุบาลหนูดี', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(11, '3041200113', 'ร.ร.ท.11', 'ร.ร.เทศบาล 11 หนองหิน', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, ''),
(12, '3041200112', 'ร.ร.ท.12', 'ร.ร.เทศบาล 12 บ้านช้าง', '03410102', 'เทศบาลนครอุดรธานี', 0, NULL, NULL, '');

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `wp_studentreg`
--

CREATE TABLE `wp_studentreg` (
  `ID` int NOT NULL,
  `reg_id` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `school_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสโรงเรียน10หลัก',
  `go_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'รหัสหน่วยงานต้นสังกัด',
  `groupsara_id` int NOT NULL COMMENT 'ID tbl wp_groupsara',
  `activity_id` int NOT NULL COMMENT 'รหัสกิจกรรม',
  `class_id` int NOT NULL COMMENT 'รหัสระดับ',
  `reg_status` int DEFAULT NULL COMMENT 'สถานะสมัคร',
  `student_prefix` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'คำนำหน้า',
  `student_firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ชื่อ',
  `student_lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'นามสกุล',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'รูปภาพ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- dump ตาราง `wp_studentreg`
--

INSERT INTO `wp_studentreg` (`ID`, `reg_id`, `school_id`, `go_id`, `groupsara_id`, `activity_id`, `class_id`, `reg_status`, `student_prefix`, `student_firstname`, `student_lastname`, `display_name`, `student_image`) VALUES
(1, '2023-08-23 16:08:55', '3041200106', '3410102', 95, 4, 4, NULL, 'ด.ญ.', 'กกก', 'กกก', NULL, NULL),
(3, '2023-08-24 10:20:56', '3041200106', '3410102', 2, 2, 4, NULL, 'ด.ญ.', 'รวิษฎา', ' อ่อนจู', NULL, NULL),
(4, '2023-08-24 11:17:20', '3041200106', '3410102', 40, 40, 4, NULL, 'ด.ช.', 'พงษ์วิชย์', 'ไชยศิริ', NULL, NULL),
(5, '2023-08-24 11:17:20', '3041200106', '3410102', 40, 40, 4, NULL, 'ด.ญ.', 'ชนิกานต์ ', 'คมณีกรณ์', NULL, NULL),
(6, '2023-08-24 11:17:20', '3041200106', '3410102', 40, 40, 4, NULL, 'ด.ญ.', 'สุกัลยา', 'อินธิเสน', NULL, NULL),
(8, '2023-08-24 14:19:58', '3041200101', '3410102', 95, 4, 4, NULL, 'ด.ช.', 'มานะ', 'ตั้งใจ', NULL, NULL),
(89, '2023-08-24 14:13:20', '3041200101', '3410102', 19, 19, 2, NULL, 'ด.ช.', 'แก้ว ', 'ว่องไว', NULL, NULL),
(90, '2023-08-24 14:13:20', '3041200101', '3410102', 19, 19, 2, NULL, 'ด.ญ.', 'ชูใจ', 'ใจดี', NULL, NULL);

-- --------------------------------------------------------

--
-- โครงสร้างตาราง `wp_teacherreg`
--

CREATE TABLE `wp_teacherreg` (
  `ID` int UNSIGNED NOT NULL,
  `reg_id` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `school_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'รหัสโรงเรียน10หลัก',
  `go_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'รหัสหน่วยงานต้นสังกัด',
  `groupsara_id` int NOT NULL,
  `activity_id` int NOT NULL COMMENT 'รหัสกิจกรรม',
  `class_id` int NOT NULL COMMENT 'รหัสระดับ',
  `reg_status` int DEFAULT NULL COMMENT 'สถานะสมัคร',
  `teacher_prefix` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'คำนำหน้า',
  `teacher_firstname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'ชื่อ',
  `teacher_lastname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL COMMENT 'นามสกุล',
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `teacher_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'รูปภาพ',
  `tel` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL COMMENT 'โทร.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- dump ตาราง `wp_teacherreg`
--

INSERT INTO `wp_teacherreg` (`ID`, `reg_id`, `school_id`, `go_id`, `groupsara_id`, `activity_id`, `class_id`, `reg_status`, `teacher_prefix`, `teacher_firstname`, `teacher_lastname`, `display_name`, `teacher_image`, `tel`) VALUES
(1, '2023-08-23 16:08:55', '3041200106', '3410102', 95, 4, 4, NULL, 'นาย', 'กกกก', 'กกกกก', NULL, NULL, '0897206819'),
(2, '2023-08-23 16:08:55', '3041200106', '3410102', 95, 4, 4, NULL, 'นาง', 'กกกกก', 'กกกกก', NULL, NULL, '0897206819'),
(4, '2023-08-24 10:20:56', '3041200106', '3410102', 2, 2, 4, NULL, 'นาง', 'ธัญชพัชร์', 'จำนงค์ศาสตร์', NULL, NULL, ''),
(5, '2023-08-24 11:17:20', '3041200106', '3410102', 40, 40, 4, NULL, 'นาง', 'วิภาภรณ์', 'อ่างมัจฉา', NULL, NULL, '0649929264'),
(6, '2023-08-24 11:17:20', '3041200106', '3410102', 40, 40, 4, NULL, 'นางสาว', 'ดวงใจ', 'สมศิริ', NULL, NULL, ''),
(8, '2023-08-24 14:19:58', '3041200101', '3410102', 95, 4, 4, NULL, 'นาง', 'ไพลิน', 'ดีงาม', NULL, NULL, ''),
(19, '2023-08-24 14:13:20', '3041200101', '3401200101', 19, 19, 2, NULL, 'นาย', 'ครู1', 'นามสกุลครู', NULL, NULL, '0840343344'),
(20, '2023-08-24 14:13:20', '3041200101', '3401200101', 19, 19, 2, NULL, '-', '-', '-', NULL, NULL, '-');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wp_schools`
--
ALTER TABLE `wp_schools`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `wp_studentreg`
--
ALTER TABLE `wp_studentreg`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `wp_teacherreg`
--
ALTER TABLE `wp_teacherreg`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `school_id` (`school_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `class_id` (`class_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wp_schools`
--
ALTER TABLE `wp_schools`
  MODIFY `ID` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wp_studentreg`
--
ALTER TABLE `wp_studentreg`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `wp_teacherreg`
--
ALTER TABLE `wp_teacherreg`
  MODIFY `ID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
