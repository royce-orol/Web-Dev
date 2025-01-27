-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 25, 2025 at 10:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `Announcements`
--

CREATE TABLE `Announcements` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Announcements`
--

INSERT INTO `Announcements` (`id`, `description`) VALUES
(8, 'This is an announcement'),
(9, 'This is the 2nd announcement');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(1, 3, 5, 'Hello Dr. Vijaya, I need your guidance on my project.', '2025-01-20 02:15:00'),
(2, 5, 3, 'Sure, let’s schedule a meeting to discuss.', '2025-01-20 02:20:00'),
(3, 3, 6, 'Hi Xi, can you review my proposal draft?', '2025-01-21 06:30:00'),
(4, 6, 3, 'Of course, please send it to me.', '2025-01-21 06:35:00'),
(5, 10, 13, 'hello', '2025-01-22 08:03:19'),
(6, 13, 7, 'hello', '2025-01-22 08:43:56'),
(7, 7, 13, 'hi', '2025-01-22 08:52:57'),
(8, 13, 7, 'sibuk ke', '2025-01-22 09:06:01'),
(9, 7, 13, 'hallo', '2025-01-22 09:10:00'),
(10, 10, 5, 'yo ', '2025-01-25 08:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `sender_id`, `message`, `created_at`) VALUES
(1, 3, 'hiihihiih', '2024-12-26 04:46:52'),
(2, 3, 'hiihihiih', '2024-12-26 04:50:53'),
(3, 13, 'Hai', '2025-01-21 15:51:13'),
(4, 3, 'Very Good', '2025-01-23 08:56:18');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `goal_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `goal_title` varchar(255) NOT NULL,
  `goal_description` text DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `meeting_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assigned_sv_id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NOT NULL,
  `status` enum('accepted','rejected','pending') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meetings`
--

INSERT INTO `meetings` (`meeting_id`, `student_id`, `assigned_sv_id`, `meeting_date`, `meeting_time`, `status`) VALUES
(1, 3, 5, '2005-12-03', '15:42:00', 'accepted'),
(2, 3, 5, '2024-12-31', '15:11:00', 'accepted'),
(3, 3, 5, '2024-12-31', '15:11:00', 'accepted'),
(4, 3, 5, '2025-01-23', '08:43:00', 'accepted'),
(5, 3, 11, '2025-02-08', '21:23:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `presentation`
--

CREATE TABLE `presentation` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `presentation`
--

INSERT INTO `presentation` (`id`, `proposal_id`, `student_id`, `date`, `time`, `created_at`, `updated_at`) VALUES
(1, 4, 13, '2025-02-01', '22:34:00', '2025-01-24 11:32:00', '2025-01-24 11:32:00'),
(2, 4, 13, '2025-01-30', '20:21:00', '2025-01-25 08:16:20', '2025-01-25 08:16:20');

-- --------------------------------------------------------

--
-- Table structure for table `proposal`
--

CREATE TABLE `proposal` (
  `proposal_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assigned_sv` int(11) DEFAULT 0,
  `marks` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposal`
--

INSERT INTO `proposal` (`proposal_id`, `sender_id`, `title`, `description`, `status`, `created_at`, `updated_at`, `assigned_sv`, `marks`) VALUES
(1, 3, 'HIV Treatment Prediction', 'Project predicting HIV Treatments', 'approved', '2024-12-26 05:13:22', '2024-12-30 08:59:07', 5, NULL),
(2, 3, 'HTML AI', 'HTML DEVELOPMENT USING AI', 'approved', '2024-12-30 09:06:11', '2024-12-30 09:06:33', 5, NULL),
(3, 3, 'Tester 1', 'Tester pertama', 'approved', '2025-01-23 10:37:17', '2025-01-23 10:38:49', 11, NULL),
(4, 13, 'Assignment 1 ', 'This is the first assignement ever', 'approved', '2025-01-24 10:49:39', '2025-01-24 10:49:59', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studsuper`
--

CREATE TABLE `studsuper` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studsuper`
--

INSERT INTO `studsuper` (`id`, `student_id`, `supervisor_id`) VALUES
(1, 17, 5),
(2, 15, 5),
(3, 13, 5),
(4, 13, 11),
(5, 3, 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','moderator','admin','supervisor') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `student_id`, `password`, `role`, `created_at`, `status`) VALUES
(3, 'Haziq', 'Zairul', 'muhdhaziq2003@gmail.com', '1221303388', '$2y$10$3wL/fhicCTVYTf2QV6tH/OuYRpG2E3NRehlspaDtpL/nLeb0pnLge', 'student', '2024-12-11 14:19:24', 'active'),
(5, 'Dr. Vijaya', 'Kumar', 'vijaya@gmail.com', '101', '$2y$10$3wL/fhicCTVYTf2QV6tH/OuYRpG2E3NRehlspaDtpL/nLeb0pnLge', 'supervisor', '2024-12-11 14:19:24', 'active'),
(6, 'Xi', 'Ze', 'xize@gmail.com', '14547383', '$2y$10$3wL/fhicCTVYTf2QV6tH/OuYRpG2E3NRehlspaDtpL/nLeb0pnLge', 'moderator', '2025-01-07 07:01:14', 'active'),
(7, 'Royce', 'orol', 'royce@gmail.com', '122133762', '$2y$10$3wL/fhicCTVYTf2QV6tH/OuYRpG2E3NRehlspaDtpL/nLeb0pnLge', 'admin', '2025-01-11 18:23:04', 'active'),
(8, 'Madam', 'Mohana', 'mohana@gmail.com', '241DM2405K', '$2y$10$N8wgMzjwRDydAYAqkIlmgOs9c3PL9bkgLlQZ2WaSzhLbkSGLwuL7S', 'admin', '2025-01-11 18:35:02', 'active'),
(9, 'Adi', 'Zuhairi', 'student@gmail.com', '1201103071', '$2y$10$nugE6r0vf25wh27QP0xgKukbEWklQaRW6lXUxpTyvW4kMuMABspK2', 'student', '2025-01-12 07:27:05', 'active'),
(10, 'Admin', '', 'admin@gmail.com', '1211102758', '$2y$10$RMU1YBf5fGcpbteqmePfMe5DgPuaYnaUAWlBPnfxE8yYI3i6t8r36', 'admin', '2025-01-12 07:31:54', 'active'),
(11, 'Supervisor', '.', 'supervisor@gmail.com', '123131213', '$2y$10$eCp7Gi3Rhk1yx/YF0mIFMuUOLlqXaowUJ6MIuazxjTGfesaj0Llea', 'supervisor', '2025-01-12 07:32:19', 'active'),
(12, 'Moderator', '', 'moderator@gmail.com', '1221303444', '$2y$10$DsW4.9hlG8do.BzRfd33ye506y/gnIGX.uP8CnpvyuGswBbzQUw8W', 'moderator', '2025-01-12 07:43:26', 'active'),
(13, 'Ammar', 'Ajwad', 'm.ammarajwad@gmail.com', '12113039991', '$2y$10$/03RMTs//MLvrJKm0bcIieHGe5fWOieiaSYsabVPANM5xzFRIM1ee', 'student', '2025-01-16 03:23:10', 'active'),
(14, 'Albab', 'Ajwad', 'encikwad@gmail.com', '1211309881', '$2y$10$Hru8F9Q2LbjsFA7ETVVrdu2k83GU39A/mMPNaIfp3isioI1Vl1B3O', 'student', '2025-01-16 03:33:43', 'active'),
(15, 'Kawa ', 'saki', 'Entah@gmail.com', '123456789', '$2y$10$i3lZdv6udNHHgq.2Dz0.3eG1T3R9KuzvsCLtewgNcLUouTbCDU3im', 'student', '2025-01-19 11:32:52', 'active'),
(16, 'Admin2', '', 'admin2@gmail.com', '1211303991', '123456789', 'admin', '2025-01-19 12:42:58', 'active'),
(17, 'Kaido', 'Ajwad', 'kais@gmail.com', '11111122121', '$2y$10$MM3TUIa26gcHxpqLKPewh.2NhS7vc.nZ4ubxd0x4p9aUFLW7V7xrS', 'student', '2025-01-21 09:35:25', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Announcements`
--
ALTER TABLE `Announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`meeting_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `assigned_sv_id` (`assigned_sv_id`);

--
-- Indexes for table `presentation`
--
ALTER TABLE `presentation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proposal_id` (`proposal_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `proposal`
--
ALTER TABLE `proposal`
  ADD PRIMARY KEY (`proposal_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `studsuper`
--
ALTER TABLE `studsuper`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Announcements`
--
ALTER TABLE `Announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `meeting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `presentation`
--
ALTER TABLE `presentation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `proposal`
--
ALTER TABLE `proposal`
  MODIFY `proposal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `studsuper`
--
ALTER TABLE `studsuper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goals`
--
ALTER TABLE `goals`
  ADD CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `goals_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_ibfk_2` FOREIGN KEY (`assigned_sv_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `presentation`
--
ALTER TABLE `presentation`
  ADD CONSTRAINT `presentation_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`proposal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `presentation_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `proposal`
--
ALTER TABLE `proposal`
  ADD CONSTRAINT `proposal_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `studsuper`
--
ALTER TABLE `studsuper`
  ADD CONSTRAINT `studsuper_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `studsuper_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
