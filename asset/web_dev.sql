-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 19, 2025 at 02:00 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Ensure proper character set
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
CREATE TABLE IF NOT EXISTS `Announcements` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Announcements`
--
INSERT INTO `Announcements` (`id`, `description`) VALUES
(3, 'Final Submission date is nearing');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--
CREATE TABLE IF NOT EXISTS `feedback` (
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
(2, 3, 'hiihihiih', '2024-12-26 04:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--
CREATE TABLE IF NOT EXISTS `goals` (
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
CREATE TABLE IF NOT EXISTS `meetings` (
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
(2, 3, 5, '2024-12-31', '15:11:00', 'pending'),
(3, 3, 5, '2024-12-31', '15:11:00', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `presentation`
--
CREATE TABLE IF NOT EXISTS `presentation` (
  `id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proposal`
--
CREATE TABLE IF NOT EXISTS `proposal` (
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
(2, 3, 'HTML AI', 'HTML DEVELOPMENT USING AI', 'approved', '2024-12-30 09:06:11', '2024-12-30 09:06:33', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE IF NOT EXISTS `users` (
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
(6, 'Xi', 'Ze', 'xize@gmail.com', '14547383', '$2y$10$3wL/fhicCTVYTf2QV6tH/OuYRpG2E3NRehlspaDtpL/nLeb0pnLge', 'moderator', '2025-01-07 07:01:14', 'active');

CREATE TABLE studsuper (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add indexes for performance optimization
CREATE INDEX idx_goals_student_id ON goals(student_id);
CREATE INDEX idx_goals_supervisor_id ON goals(supervisor_id);
CREATE INDEX idx_meetings_status ON meetings(status);
CREATE INDEX idx_proposal_status ON proposal(status);

-- Add comments
COMMENT ON TABLE `users` IS 'Stores user data including roles and authentication details.';
COMMENT ON COLUMN `users`.`role` IS 'Defines the role of the user: student, moderator, admin, supervisor.';
COMMENT ON TABLE `goals` IS 'Stores goals for students assigned to supervisors.';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
