-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 14, 2024 at 01:34 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xampp`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `plain_password`, `roles`) VALUES
(11, 'admin', '$2y$13$13LveppfFhR7OIsgcsVexeglcnyS4AuHnnX1PA3bsXSyBYUf3PTEe', 'admin', '[\"ROLE_ADMIN\"]'),
(14, 'Sam', '$2y$13$Kjn9Ibe9UW4NG0IMunq3Dejb.GvhY2clc0sIlfh.wXsN4WbkuwNSS', 'Sam', '[\"ROLE_USER\"]'),
(30, 'Test', '$2y$13$JD27nMq2ojvx2t1agPp0Ru9mq.LQEZoOc7iarnZtKca4LryJyzE3m', 'Test', '[\"ROLE_USER\"]'),
(31, 'Rohit', '$2y$13$vxjHHmUVgvO242wZJ66KcOCJdrz6w8zT5wCUkXQXyGOBU9fVDtTp6', 'Rohit', '[\"ROLE_ADMIN\"]'),
(32, 'Ramesh', '$2y$13$bJjsiufgWytKq2QPNVzmROZSe9K.TpznSUz/5a7EQzla6UcvvAdCu', 'Ramesh', '[\"ROLE_USER\"]'),
(33, 'Ram', '$2y$13$7V19OXQN4GnQ5sK3xurXAuHgUXNVSr.JldV2YEH9FgrUQXYApm7/W', 'Ram', '[\"ROLE_ADMIN\"]'),
(34, 'Mahadev', '$2y$13$.vMhHoOALkskrW/53WL2XuiRiiKsspTdwWjE6AQZGuxezR6moXrz2', 'Mahadev', '[\"ROLE_USER\"]'),
(35, 'System', '$2y$13$l3SWFHdyN/1kGwEmGI3YmOiqvCsUvhvqlkUeBsn5n7E8qfgDv2x3G', 'System', '[\"ROLE_ADMIN\"]'),
(36, 'Username', '$2y$13$qpAXdJljELMhBtATBYkB.e3.sQeaaE1NySzhI31ICabPkdZo./oES', 'Password', '[\"ROLE_USER\"]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
