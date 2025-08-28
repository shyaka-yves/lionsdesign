-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2025 at 03:27 PM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `liondesign_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(100) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin_activity_log`
--

INSERT INTO `admin_activity_log` (`id`, `admin_id`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 7, 'create', 'product', NULL, 'title=wooden, price=20000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:47:38'),
(2, 7, 'update', 'product', 23, 'title=wooden333, price=20000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:52:50'),
(3, 7, 'update', 'product', 23, 'title=wooden3336, price=20000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 08:53:18'),
(4, 1, 'delete', 'user', 11, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:26:47'),
(5, 1, 'delete', 'user', 17, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:26:55'),
(6, 1, 'delete', 'user', 14, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:26:58'),
(7, 1, 'delete', 'user', 10, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:27:01'),
(8, 1, 'delete', 'user', 9, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:27:08'),
(9, 1, 'delete', 'user', 8, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:27:12'),
(10, 1, 'delete', 'user', 6, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:27:17'),
(11, 1, 'delete', 'user', 5, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 09:27:21'),
(12, 1, 'update', 'product', 22, 'title=Crystal black stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:53:57'),
(13, 1, 'update', 'product', 22, 'title=Crystal black stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:58:22'),
(14, 1, 'activate', 'product', 22, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:58:32'),
(15, 1, 'deactivate', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:58:52'),
(16, 1, 'deactivate', 'product', 22, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:58:55'),
(17, 1, 'deactivate', 'product', 21, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:58:59'),
(18, 1, 'activate', 'product', 21, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:59:17'),
(19, 1, 'activate', 'product', 22, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:59:38'),
(20, 1, 'activate', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-08-08 12:59:40'),
(21, 1, 'create', 'category', 9, 'name=Pens', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 08:25:38'),
(22, 1, 'create', 'product', NULL, 'title=black pen, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 08:26:36'),
(23, 1, 'grant_admin', 'user', 20, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 08:30:08'),
(24, 20, 'update', 'product', 24, 'title=black pen, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 08:43:43'),
(25, 1, 'revoke_admin', 'user', 7, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 08:50:29'),
(26, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:53:21'),
(27, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:53:29'),
(28, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:53:35'),
(29, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:53:42'),
(30, 1, 'delete', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:53:55'),
(31, 1, 'activate', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:54:08'),
(32, 1, 'activate', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:54:11'),
(33, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:54:19'),
(34, 1, 'activate', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:54:26'),
(35, 1, 'update', 'product', 24, 'title=black pen, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:54:50'),
(36, 1, 'update', 'category', 7, 'name=Caps', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:55:07'),
(37, 1, 'delete', 'category', 9, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:55:14'),
(38, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:06'),
(39, 1, 'activate', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:12'),
(40, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:15'),
(41, 1, 'update', 'product', 24, 'title=black pen, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:40'),
(42, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:49'),
(43, 1, 'activate', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 14:58:53'),
(44, 1, 'update', 'product', 24, 'title=black pen, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:02:30'),
(45, 1, 'update', 'product', 23, 'title=wooden3336, price=20000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:02:52'),
(46, 1, 'delete', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:02:59'),
(47, 1, 'activate', 'product', 24, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:32:45'),
(48, 1, 'delete', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:32:54'),
(49, 1, 'delete', 'product', 15, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:33:00'),
(50, 1, 'activate', 'product', 15, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:33:11'),
(51, 1, 'update', 'product', 17, 'title=Yellow cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:33:47'),
(52, 1, 'update', 'product', 17, 'title=Yellow cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:34:11'),
(53, 1, 'deactivate', 'product', 17, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:34:23'),
(54, 1, 'activate', 'product', 23, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:34:54'),
(55, 1, 'activate', 'product', 17, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:34:59'),
(56, 1, 'update', 'product', 17, 'title=Yellow cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:36:33'),
(57, 1, 'update', 'product', 17, 'title=Yellow cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-09 15:52:17'),
(58, 1, 'update', 'product', 22, 'title=Crystal black stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:51:37'),
(59, 1, 'update', 'product', 15, 'title=Black Cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:53:23'),
(60, 1, 'update', 'product', 16, 'title=White cap, price=2000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:53:36'),
(61, 1, 'update', 'product', 18, 'title=Crystal hexagon, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:53:54'),
(62, 1, 'update', 'product', 19, 'title=Crystal blue stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:54:10'),
(63, 1, 'update', 'product', 20, 'title=Crystal long wooden stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:54:31'),
(64, 1, 'update', 'product', 20, 'title=Crystal long wooden stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:55:08'),
(65, 1, 'update', 'product', 21, 'title=Crystal golden stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:55:26'),
(66, 1, 'update', 'product', 22, 'title=Crystal black stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:55:49'),
(67, 1, 'delete', 'product', 20, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:55:54'),
(68, 1, 'delete', 'product', 20, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:56:19'),
(69, 1, 'create', 'product', NULL, 'title=Crystal black stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:15'),
(70, 1, 'delete', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:24'),
(71, 1, 'activate', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:30'),
(72, 1, 'delete', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:33'),
(73, 1, 'delete', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:35'),
(74, 1, 'delete', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 08:59:39'),
(75, 1, 'delete', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 09:01:05'),
(76, 1, 'activate', 'product', 25, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 09:01:17'),
(77, 1, 'create', 'product', NULL, 'title=Crystal Golden stand, price=25000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-10 09:04:04'),
(78, 1, 'create', 'category', 10, 'name=Gift Sets', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:08:09'),
(79, 1, 'create', 'product', NULL, 'title=Wooden gift set, price=20000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:11:32'),
(80, 1, 'create', 'product', NULL, 'title=wooden gift set(Large), price=35000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:14:34'),
(81, 1, 'create', 'product', NULL, 'title=wooden gift set(Large), price=35000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:15:55'),
(82, 1, 'delete', 'product', 29, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:16:47'),
(83, 1, 'create', 'product', NULL, 'title=Gift set (medium), price=30000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:52:10'),
(84, 1, 'create', 'product', NULL, 'title=Gift set (Large), price=45000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:54:55'),
(85, 1, 'create', 'product', NULL, 'title=Gift set(Large), price=50000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 08:59:24'),
(86, 1, 'delete', 'user', 7, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:02:27'),
(87, 1, 'delete', 'user', 19, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:02:39'),
(88, 1, 'delete', 'user', 21, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:02:43'),
(89, 1, 'delete', 'user', 22, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:02:52'),
(90, 1, 'create', 'service', 6, 'title=Large formar', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:12:39'),
(91, 1, 'delete', 'service', 6, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:13:07'),
(92, 1, 'deactivate', 'product', 32, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:13:41'),
(93, 1, 'activate', 'product', 32, '', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:14:14'),
(94, 1, 'create', 'product', NULL, 'title=Wooden plaque, price=45000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:30:31'),
(95, 1, 'create', 'category', 11, 'name=Magnet', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:32:57'),
(96, 1, 'create', 'product', NULL, 'title=Rectangular magnets, price=1000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:34:28'),
(97, 1, 'create', 'product', NULL, 'title=Rounded magnets, price=1000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:36:30'),
(98, 1, 'create', 'category', 12, 'name=Mugs', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:38:46'),
(99, 1, 'create', 'product', NULL, 'title=magic mug, price=4000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:40:18'),
(100, 1, 'create', 'product', NULL, 'title=magic mug, price=4000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:41:09'),
(101, 1, 'create', 'product', NULL, 'title=Magic mug, price=4000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:45:45'),
(102, 1, 'update', 'product', 38, 'title=Magic mug, price=4000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 09:46:02'),
(103, 1, 'create', 'product', NULL, 'title=Mug, price=3000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:15:25'),
(104, 1, 'create', 'product', NULL, 'title=Handle Travel Mug, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:18:09'),
(105, 1, 'create', 'product', NULL, 'title=Thermal mug, price=11500', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:20:08'),
(106, 1, 'create', 'category', 13, 'name=Tapes', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:35:18'),
(107, 1, 'create', 'product', NULL, 'title=Double tape, price=15000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:36:48'),
(108, 1, 'create', 'category', 14, 'name=Bottles', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:38:35'),
(109, 1, 'create', 'product', NULL, 'title=Bottle 750ml, price=10000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:40:12'),
(110, 1, 'create', 'product', NULL, 'title=Transparent Bottle, price=7000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:42:05'),
(111, 1, 'create', 'product', NULL, 'title=Flask, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:44:47'),
(112, 1, 'create', 'product', NULL, 'title=Temperature bottles, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:46:53'),
(113, 1, 'create', 'product', NULL, 'title=Temperature bottles, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:47:55'),
(114, 1, 'create', 'product', NULL, 'title=Temperature bottles, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:48:19'),
(115, 1, 'create', 'product', NULL, 'title=Temperature bottles, price=12000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 12:48:33'),
(116, 1, 'create', 'category', 15, 'name=Umbrella', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:38:33'),
(117, 1, 'create', 'product', NULL, 'title=White umbrella (Large), price=10000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:40:54'),
(118, 1, 'create', 'product', NULL, 'title=Black umbrella (Large), price=10000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:42:37'),
(119, 1, 'create', 'product', NULL, 'title=Black umbrella (Small), price=7000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:43:54'),
(120, 1, 'create', 'category', 16, 'name=Pens', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:45:11'),
(121, 1, 'create', 'product', NULL, 'title=Black metal Pen, price=1000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:46:34'),
(122, 1, 'create', 'product', NULL, 'title=Pen, price=1000', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 13:47:32'),
(123, 1, 'create', 'service', 7, 'title=Large format printing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:06:57'),
(124, 1, 'update', 'service', 7, 'title=Large format printing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:08:22'),
(125, 1, 'create', 'service', 8, 'title=UV Printing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:12:56'),
(126, 1, 'update', 'service', 8, 'title=UV Printing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:13:12'),
(127, 1, 'create', 'service', 9, 'title=Fiber Laser marking', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:42:26'),
(128, 1, 'create', 'service', 10, 'title=DTF Printing', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:49:52'),
(129, 1, 'create', 'service', 11, 'title=Electric paper cutter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:53:21'),
(130, 1, 'update', 'service', 11, 'title=Electric paper cutter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 14:59:17'),
(131, 1, 'update', 'service', 11, 'title=Electric paper cutter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 15:01:23'),
(132, 1, 'update', 'service', 9, 'title=Fiber Laser marking', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 15:02:05'),
(133, 1, 'create', 'service', 12, 'title=Engraving ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 15:08:04'),
(134, 1, 'update', 'service', 11, 'title=Electric paper cutter', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 15:09:23'),
(135, 1, 'update', 'service', 9, 'title=Fiber Laser marking', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-11 15:09:42'),
(136, 1, 'update_order_status', 'order', 24, 'Status set to paid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-13 10:07:48'),
(137, 1, 'update_order_status', 'order', 25, 'Status set to paid', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 14:05:41'),
(138, 1, 'update_order_status', 'order', 25, 'Status set to shipped', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-08-14 14:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `session_id`, `product_id`, `quantity`, `created_at`) VALUES
(96, 'e0ao2kpcu2fn1katc9vp0o8am8', 46, 1, '2025-08-14 07:52:12'),
(97, 'e0ao2kpcu2fn1katc9vp0o8am8', 45, 1, '2025-08-14 07:52:16');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `slug`, `created_at`) VALUES
(7, 'Caps', 'Classic unisex cap made from high-quality cotton, designed for comfort and style. Adjustable strap for a perfect fit.', 'Caps', '2025-08-01 15:27:40'),
(8, 'Crystal awards', 'Premium crystal award with a sleek design, perfect for recognizing achievements and celebrating success.', 'Crystal awards', '2025-08-01 15:39:22'),
(10, 'Gift Sets', 'Curated premium gift sets designed to impress for every occasion.', 'Gift set', '2025-08-11 08:08:09'),
(11, 'Magnet', 'Durable magnetic fasteners designed to securely hold name tags without damaging clothing.', 'Magnets', '2025-08-11 09:32:57'),
(12, 'Mugs', 'High-quality mugs designed for both style and durability, perfect for enjoying coffee, tea, or any beverage.', 'mugs', '2025-08-11 09:38:46'),
(13, 'Tapes', 'Strip of material that is sticky on one side or both and used for fastening, sealing, or holding things together.', 'Tapes', '2025-08-11 12:35:18'),
(14, 'Bottles', 'container, typically made of plastic, glass, or metal, with a narrow neck and a mouth that can be sealed with a cap, lid, stopper, or cork.', 'Bottles', '2025-08-11 12:38:35'),
(15, 'Umbrella', 'Portable, handheld device designed to provide protection from rain or sunlight.', 'Umbrellas', '2025-08-11 13:38:33'),
(16, 'Pens', 'Handheld tool used to apply ink to a surface, typically paper, for writing or drawing.', 'Pens', '2025-08-11 13:45:11');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`, `updated_at`) VALUES
(9, 'pacifique twitegure', 'twitepacifique@gmail.com', 'Appreciation', 'thank you', 'read', '2025-08-10 06:58:06', '2025-08-11 07:54:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `payment_method`, `shipping_address`, `phone`, `created_at`, `updated_at`) VALUES
(17, 18, '12000.00', 'paid', 'mtn_mobile_money', 'kigali', '0786694613', '2025-08-08 09:39:46', '2025-08-08 09:39:46'),
(18, 1, '2000.00', 'paid', 'mtn_mobile_money', 'kigali', '0791901949', '2025-08-08 12:52:17', '2025-08-09 15:37:34'),
(20, 1, '2000.00', 'paid', 'mtn_mobile_money', 'kigali', '0791901949', '2025-08-09 15:35:39', '2025-08-09 15:35:39'),
(23, 1, '1000.00', 'cancelled', 'mtn_mobile_money', 'Kigali', '0791901949', '2025-08-13 10:04:29', '2025-08-13 10:05:27'),
(24, 1, '1000.00', 'paid', 'mtn_mobile_money', 'kigali', '0791901949', '2025-08-13 10:07:30', '2025-08-13 10:07:48'),
(25, 1, '1000.00', 'shipped', 'mtn_mobile_money', 'kigalii', '0791901949', '2025-08-14 14:04:45', '2025-08-14 14:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(31, 17, 17, 3, '2000.00'),
(32, 17, 16, 3, '2000.00'),
(33, 18, 17, 1, '2000.00'),
(36, 20, 17, 1, '2000.00'),
(41, 23, 54, 1, '1000.00'),
(42, 24, 54, 1, '1000.00'),
(43, 25, 54, 1, '1000.00');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `type` enum('signup','password_reset') NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'jado@gmail.com', '799c49adf1f2448649c838496b94bf44675b43bbd155610387e5a184d16f76ee', '2025-08-01 11:07:14', '2025-08-01 10:07:14');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `image`, `category_id`, `stock_quantity`, `is_featured`, `is_active`, `created_at`, `updated_at`) VALUES
(15, 'Black Cap', 'Classic black unisex cap made from durable cotton with an adjustable strap for a perfect fit.', '2000.00', 'assets/images/products/68985e0354e32.jpg', 7, 999, 1, 1, '2025-08-01 15:34:35', '2025-08-10 08:53:23'),
(16, 'White cap', 'Classic white unisex cap made from durable cotton with an adjustable strap for a perfect fit.', '2000.00', 'assets/images/products/68985e1067f44.webp', 7, 1000, 1, 1, '2025-08-01 15:36:26', '2025-08-10 08:53:36'),
(17, 'Yellow cap', 'Classic yellow unisex cap made from durable cotton with an adjustable strap for a perfect fit.', '2000.00', 'assets/images/products/68976eb11c39b.webp', 7, 999, 1, 1, '2025-08-01 15:37:42', '2025-08-10 16:22:18'),
(18, 'Crystal hexagon', 'Elegant hexagonal crystal award designed for recognition and celebration of excellence.', '25000.00', 'assets/images/products/68985e22b7201.jpg', 8, 100, 1, 1, '2025-08-01 15:42:14', '2025-08-10 08:53:54'),
(19, 'Crystal blue stand', 'Premium crystal award with an elegant blue stand, perfect for celebrating achievements.', '25000.00', 'assets/images/products/68985e323401f.jpg', 8, 96, 1, 1, '2025-08-04 09:44:46', '2025-08-10 08:54:10'),
(21, 'Crystal golden stand', 'Luxurious crystal award with a golden stand, perfect for prestigious recognition.', '25000.00', 'assets/images/products/68985e7e1c498.jpg', 8, 100, 1, 1, '2025-08-04 09:49:11', '2025-08-10 08:55:26'),
(25, 'Crystal black stand', 'Black Wooden Display Stand for Crystal Sphere Quartz Ball Holder', '25000.00', 'assets/images/products/Crystal Black stand.jpg', 8, 49, 1, 1, '2025-08-10 08:59:15', '2025-08-10 16:22:18'),
(26, 'Crystal Golden stand', 'A premium crystal award featuring a sleek golden stand, blending elegance and prestige.', '25000.00', 'assets/images/products/68986084dea92.jpg', 8, 49, 1, 1, '2025-08-10 09:04:04', '2025-08-10 16:22:18'),
(27, 'Wooden gift set', 'Contains wooden pen, key holder and card holder', '20000.00', 'assets/images/products/6899a5b4b6db1.png', 10, 50, 1, 1, '2025-08-11 08:11:32', '2025-08-11 08:11:32'),
(28, 'wooden gift set(Large)', 'Contains wooden pen, flash disk, card holder, key holder and temperature bottle', '35000.00', 'assets/images/products/Wooden Gift set(Large).jpg', 10, 50, 1, 1, '2025-08-11 08:14:34', '2025-08-11 08:15:49'),
(30, 'Gift set (medium)', 'Contains pen, bottle, agenda, flash disk', '30000.00', 'assets/images/products/6899af3aa3a64.jpg', 10, 50, 1, 1, '2025-08-11 08:52:10', '2025-08-11 08:52:10'),
(31, 'Gift set (Large)', 'Contains Bottle,pen, flash disk, agenda, key holder, card holder', '45000.00', 'assets/images/products/Gift set1 (Large).jpg', 10, 50, 1, 1, '2025-08-11 08:54:55', '2025-08-11 08:55:57'),
(32, 'Gift set(Large)', 'Contains bottle, mug, key holder, pen and agenda', '50000.00', 'assets/images/products/6899b0ec78c0f.jpg', 10, 50, 1, 1, '2025-08-11 08:59:24', '2025-08-11 09:14:14'),
(33, 'Wooden plaque', 'A classic wooden plaque designed to honor achievements and milestones with timeless elegance.', '45000.00', 'assets/images/products/6899b837d44a6.jpeg', 8, 50, 1, 1, '2025-08-11 09:30:31', '2025-08-11 09:30:31'),
(34, 'Rectangular magnets', 'Strong rectangular magnetic fasteners ideal for securing name tags without pins or clips.', '1000.00', 'assets/images/products/6899b9245762b.jpg', 11, 1000, 0, 1, '2025-08-11 09:34:28', '2025-08-11 09:34:28'),
(35, 'Rounded magnets', 'Strong rounded magnetic fasteners ideal for securing name tags without pins or clips.', '1000.00', 'assets/images/products/6899b99e5e3c8.jpg', 11, 1000, 0, 1, '2025-08-11 09:36:30', '2025-08-11 09:36:30'),
(38, 'Magic mug', 'A heat-sensitive mug that reveals hidden designs or colors when filled with hot liquid.', '4000.00', 'assets/images/products/6899bbdac9fd1.png', 12, 100, 1, 1, '2025-08-11 09:45:45', '2025-08-11 09:46:02'),
(39, 'Mug', 'classic and versatile drinking vessel, primarily used for hot beverages like coffee or tea.', '3000.00', 'assets/images/products/6899dedd3053f.png', 12, 1000, 0, 1, '2025-08-11 12:15:25', '2025-08-11 12:15:25'),
(40, 'Handle Travel Mug', 'Portable container designed for carrying hot or cold beverages on the go.', '12000.00', 'assets/images/products/6899df815a843.jpg', 12, 100, 1, 1, '2025-08-11 12:18:09', '2025-08-11 12:18:09'),
(41, 'Thermal mug', 'known as an insulated mug or thermos mug, is a type of drinking container designed to keep hot beverages hot and cold beverages cold for an extended period.', '11500.00', 'assets/images/products/6899dff822e11.jpg', 12, 100, 1, 1, '2025-08-11 12:20:08', '2025-08-11 12:20:08'),
(42, 'Double tape', 'Has adhesive on both sides, allowing it to fasten two surfaces together.', '15000.00', 'assets/images/products/6899e3e008830.jpg', 13, 50, 0, 1, '2025-08-11 12:36:48', '2025-08-11 12:36:48'),
(43, 'Bottle 750ml', 'Designed for storing and transporting liquids such as water, milk, beverages.', '10000.00', 'assets/images/products/6899e4aca4f97.jpg', 14, 50, 1, 1, '2025-08-11 12:40:12', '2025-08-11 12:40:12'),
(44, 'Transparent Bottle', 'Typically made of clear plastic, that allows its contents to be seen.', '7000.00', 'assets/images/products/6899e51d33fc7.jpg', 14, 50, 1, 1, '2025-08-11 12:42:05', '2025-08-11 12:42:05'),
(45, 'Flask', 'Container designed to hold a beverage', '12000.00', 'assets/images/products/6899e5bf3ddb1.jpg', 14, 50, 1, 1, '2025-08-11 12:44:47', '2025-08-11 12:44:47'),
(46, 'Temperature bottles', 'Smart water bottle or an insulated bottle with a display, is a type of bottle that can show you the temperature of the liquid inside.', '12000.00', 'assets/images/products/Temperature Bottles.jpg', 14, 50, 1, 1, '2025-08-11 12:46:53', '2025-08-11 12:47:43'),
(50, 'White umbrella (Large)', 'Spacious, handheld umbrella with a wide canopy made of white or off-white fabric.', '10000.00', 'assets/images/products/6899f2e697bf4.png', 15, 1000, 1, 1, '2025-08-11 13:40:54', '2025-08-11 13:40:54'),
(51, 'Black umbrella (Large)', 'spacious, handheld umbrella with a wide canopy made of black or off-blackfabric.', '10000.00', 'assets/images/products/6899f34cf3fb8.png', 15, 100, 1, 1, '2025-08-11 13:42:37', '2025-08-11 13:42:37'),
(52, 'Black umbrella (Small)', 'spacious, handheld umbrella with a small canopy made of white or off-white fabric.', '7000.00', 'assets/images/products/6899f39aeb9cf.jpg', 15, 100, 1, 1, '2025-08-11 13:43:54', '2025-08-11 13:43:54'),
(53, 'Black metal Pen', 'Writing instrument crafted from a durable metal casing with a black finish.', '1000.00', 'assets/images/products/6899f43ae9577.png', 16, 1000, 0, 1, '2025-08-11 13:46:34', '2025-08-11 13:46:34'),
(54, 'Pen', 'writing instrument crafted from a durable metal casing with a black finish.', '1000.00', 'assets/images/products/6899f4747bbef.jpeg', 16, 998, 0, 1, '2025-08-11 13:47:32', '2025-08-14 14:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `image`, `price`, `created_at`, `updated_at`) VALUES
(7, 'Large format printing', 'Printing Banners, Stickers, pull-ups, tear drop, one-way, back light,...', 'uploads/services/6899f955f2426.jpg', 'From 3000 Rwf', '2025-08-11 14:06:57', '2025-08-11 14:08:21'),
(8, 'UV Printing', 'Printing on pens, phone covers, agenda, bags and other flat materials', 'uploads/services/6899fa7851a29.jpg', 'From 5000 Rwf', '2025-08-11 14:12:56', '2025-08-11 14:13:12'),
(9, 'Fiber Laser marking', 'For marking metal pens, bottles, agenda, key holders and other plastic and metal materials', 'uploads/services/689a07b5f19a1.jpg', 'From 1000 Rwf', '2025-08-11 14:42:26', '2025-08-11 15:09:41'),
(10, 'DTF Printing', 'For printing artworks to brand on t-shirts, caps, hoodies,....', 'uploads/services/689a031007446.jpg', 'from 2000 Rwf', '2025-08-11 14:49:52', '2025-08-11 14:49:52'),
(11, 'Electric paper cutter', 'For cutting paper into small pieces', 'uploads/services/689a07a349e08.jpeg', 'From 500 Rwf', '2025-08-11 14:53:21', '2025-08-11 15:09:23'),
(12, 'Engraving ', 'For branding on matelials like wooden pen, cutting acrylic, woods and other wooden materials', 'uploads/services/689a075449752.webp', 'From 2000 Rwf', '2025-08-11 15:08:04', '2025-08-11 15:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `service_requests`
--

CREATE TABLE `service_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `file` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('super','admin','customer') DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `phone`, `address`, `created_at`, `updated_at`, `role`) VALUES
(1, 'admin@liondesign.com', '$2y$10$oUYpKAvkcK1wAbgHNvLFZOqYRSLVpxEkoWQi9F0dWsj9x.QRKAO0i', 'Yves', 'Shyaka', '', NULL, '2025-07-26 12:47:41', '2025-08-07 08:22:54', 'super'),
(18, 'shadadimanzi57@gmail.com', '$2y$10$TpmstnTDTIXZ7tcOigaP7eEUFJJ5.7JolAyDK1tEgFyJMW7ZIDGwW', 'shadadi', 'manzi', '0786694613', 'kigali', '2025-08-08 09:36:57', '2025-08-08 09:36:57', 'customer'),
(20, 'belyse.igiraneza2000@gmail.com', '$2y$10$dED0AIhw//M7USMRp5ZQ2OiI0BwrMt35jpHL/nesZkoUQZX1p9o02', 'IGIRANEZA', 'Belyse', '0788921533', 'Kigali-Rwanda', '2025-08-09 08:29:17', '2025-08-09 08:30:08', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_admin_id` (`admin_id`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_type` (`email`,`type`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `service_requests`
--
ALTER TABLE `service_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_requests`
--
ALTER TABLE `service_requests`
  ADD CONSTRAINT `service_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_requests_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
