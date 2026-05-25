-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2026 at 05:48 AM
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
-- Database: `srmss_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `license_number` varchar(50) DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('available','on_duty','off') DEFAULT NULL,
  `working_hours` int(11) DEFAULT 0,
  `depot` varchar(100) DEFAULT 'Colombo Regional Depot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `license_number`, `license_expiry`, `phone`, `status`, `working_hours`, `depot`) VALUES
(1, 'Kamal Perera', 'B1234567', '2027-06-30', '0771234567', 'available', 40, 'Colombo Regional Depot'),
(2, 'Nimal Silva', 'B2345678', '2026-12-15', '0762345678', 'on_duty', 35, 'Colombo Regional Depot'),
(3, 'Suresh Fernando', 'B3456789', '2028-03-20', '0753456789', 'on_duty', 42, 'Colombo Regional Depot'),
(5, 'Ajith Bandara', 'B4567890', '2027-09-10', '0774567890', 'on_duty', 38, 'Colombo Regional Depot'),
(6, 'Roshan Jayasinghe', 'B5678901', '2026-08-25', '0765678901', 'on_duty', 40, 'Colombo Regional Depot'),
(7, 'Chamara Dissanayake', 'B6789012', '2028-11-05', '0776789012', 'on_duty', 45, 'Colombo Regional Depot'),
(8, 'Pradeep Gunasekara', 'B7890123', '2027-04-18', '0787890123', 'on_duty', 38, 'Colombo Regional Depot'),
(9, 'Lasith Madushanka', 'B8901234', '2026-07-22', '0778901234', 'on_duty', 40, 'Colombo Regional Depot'),
(10, 'Nuwan Rathnayake', 'B9012345', '2028-01-30', '0769012345', 'available', 35, 'Colombo Regional Depot'),
(11, 'Dinesh Wickramasinghe', 'B0123456', '2027-10-14', '0750123456', 'available', 42, 'Colombo Regional Depot'),
(12, 'Saman Kumara', 'C1234567', '2027-08-15', '0771122334', 'on_duty', 40, 'Colombo Regional Depot'),
(13, 'Ranjith Perera', 'C2345678', '2028-02-20', '0762233445', 'on_duty', 38, 'Colombo Regional Depot'),
(14, 'Mahesh Silva', 'C3456789', '2027-11-10', '0753344556', 'on_duty', 42, 'Colombo Regional Depot'),
(15, 'Saman Kumara', 'C1234567', '2027-08-15', '0771122334', 'available', 40, 'Kandy Regional Depot'),
(16, 'Ranjith Perera', 'C2345678', '2028-02-20', '0762233445', 'available', 38, 'Kandy Regional Depot'),
(17, 'Mahesh Silva', 'C3456789', '2027-11-10', '0753344556', 'available', 42, 'Kandy Regional Depot');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_logs`
--

CREATE TABLE `fuel_logs` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `liters` float DEFAULT NULL,
  `cost` float DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_logs`
--

INSERT INTO `fuel_logs` (`id`, `vehicle_id`, `date`, `liters`, `cost`, `notes`) VALUES
(2, 1, '2026-05-19', 60, 18000, 'Filled at Colombo depot'),
(3, 2, '2026-05-19', 55, 16500, 'Filled at Galle depot'),
(4, 4, '2026-05-19', 80, 24000, 'Filled at Matara depot'),
(5, 5, '2026-05-18', 45, 13500, 'Filled at Colombo depot'),
(6, 6, '2026-05-18', 45, 10500, 'Filled at Negombo depot');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `type` enum('routine','corrective','emergency') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `cost` float DEFAULT NULL,
  `next_service_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `vehicle_id`, `date`, `type`, `description`, `cost`, `next_service_date`) VALUES
(2, 10, '2026-05-15', 'emergency', 'Engine overheating repair', 25000, '2026-06-15'),
(3, 1, '2026-05-10', 'routine', 'Oil change and filter replacement', 5000, '2026-08-10'),
(4, 2, '2026-05-12', 'corrective', 'Brake pad replacement', 8500, '2026-09-12');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `start_point` varchar(100) DEFAULT NULL,
  `end_point` varchar(100) DEFAULT NULL,
  `stops` text DEFAULT NULL,
  `distance` float DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `start_lat` decimal(10,8) DEFAULT NULL,
  `start_lng` decimal(11,8) DEFAULT NULL,
  `end_lat` decimal(10,8) DEFAULT NULL,
  `end_lng` decimal(11,8) DEFAULT NULL,
  `depot` varchar(100) DEFAULT 'Colombo Regional Depot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `start_point`, `end_point`, `stops`, `distance`, `driver_id`, `vehicle_id`, `start_lat`, `start_lng`, `end_lat`, `end_lng`, `depot`) VALUES
(1, 'Colombo', 'Kandy', 'Kadawatha, Nittambuwa, Kegalle', 116, 1, 1, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(4, 'Colombo', 'Galle', 'Kalutara, Aluthgama, Hikkaduwa', 119, 2, 2, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(5, 'Colombo', 'Matara', 'Panadura, Kalutara, Galle', 160, 3, 4, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(6, 'Colombo', 'Kurunegala', 'Kelaniya, Gampaha, Veyangoda', 94, 5, 5, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(7, 'Colombo', 'Negombo', 'Peliyagoda, Wattala, Seeduwa', 37, 6, 6, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(8, 'Colombo', 'Ratnapura', 'Padukka, Avissawella, Kuruwita', 101, 7, 7, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(9, 'Colombo', 'Badulla', 'Kandy, Nuwara Eliya, Bandarawela', 230, 8, 8, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(10, 'Colombo', 'Anuradhapura', 'Kurunegala, Dambulla, Kekirawa', 205, 9, 9, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(11, 'Kandy', 'Colombo', 'Kegalle, Nittambuwa, Kadawatha', 116, 12, 12, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(12, 'Kandy', 'Jaffna', 'Dambulla, Anuradhapura, Vavuniya', 317, 13, 13, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(13, 'Kandy', 'Badulla', 'Matale, Dambulla, Mahiyanganaya', 140, 14, 14, NULL, NULL, NULL, NULL, 'Colombo Regional Depot'),
(14, 'Kandy', 'Badulla', 'Matale, Dambulla, Mahiyanganaya', 140, 14, 14, NULL, NULL, NULL, NULL, 'Colombo Regional Depot');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `route_id` int(11) DEFAULT NULL,
  `departure_time` datetime DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `status` enum('on_time','delayed','completed') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `route_id`, `departure_time`, `arrival_time`, `status`) VALUES
(5, 1, '2026-05-20 06:00:00', '2026-05-20 09:00:00', 'on_time'),
(6, 4, '2026-05-20 07:00:00', '2026-05-20 10:00:00', 'on_time'),
(7, 5, '2026-05-20 08:00:00', '2026-05-20 00:00:00', 'delayed'),
(8, 6, '2026-05-20 09:00:00', '2026-05-20 11:30:00', 'on_time'),
(9, 7, '2026-05-20 10:00:00', '2026-05-20 11:00:00', 'completed'),
(10, 8, '2026-05-20 11:00:00', '2026-05-20 14:00:00', 'on_time'),
(11, 9, '2026-05-20 17:00:00', '2026-05-20 13:00:00', 'on_time'),
(12, 10, '2026-05-20 18:30:00', '2026-05-20 12:30:00', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','supervisor','clerk') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin User', 'admin@srmss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
(2, 'Supervisor Silva', 'supervisor@srmss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'supervisor'),
(3, 'Clerk Perera', 'clerk@srmss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'clerk');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `registration` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `mileage` float DEFAULT NULL,
  `status` enum('available','on_route','maintenance') DEFAULT NULL,
  `depot` varchar(100) DEFAULT 'Colombo Regional Depot'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `registration`, `capacity`, `mileage`, `status`, `depot`) VALUES
(1, 'NB-1234', 50, 45000, 'available', 'Colombo Regional Depot'),
(2, 'NC-5678', 45, 32000, 'maintenance', 'Colombo Regional Depot'),
(3, 'ND-9012', 55, 67000, 'on_route', 'Colombo Regional Depot'),
(4, 'ND-9012', 55, 67000, 'on_route', 'Colombo Regional Depot'),
(5, 'NE-3456', 40, 28000, 'on_route', 'Colombo Regional Depot'),
(6, 'NF-7890', 52, 51000, 'on_route', 'Colombo Regional Depot'),
(7, 'NG-2345', 45, 38000, 'on_route', 'Colombo Regional Depot'),
(8, 'NH-6789', 55, 72000, 'on_route', 'Colombo Regional Depot'),
(9, 'NI-0123', 40, 19000, 'on_route', 'Colombo Regional Depot'),
(10, 'NJ-4567', 52, 55000, 'maintenance', 'Colombo Regional Depot'),
(11, 'NK-8901', 45, 43000, 'available', 'Colombo Regional Depot'),
(12, 'KB-1111', 52, 38000, 'on_route', 'Colombo Regional Depot'),
(13, 'KB-2222', 45, 29000, 'on_route', 'Colombo Regional Depot'),
(14, 'KB-3333', 55, 51000, 'on_route', 'Colombo Regional Depot');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD CONSTRAINT `fuel_logs_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
