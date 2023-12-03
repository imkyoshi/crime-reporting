-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2023 at 06:41 PM
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
-- Database: `crime-app`
--

-- --------------------------------------------------------

--
-- Table structure for table `crime_category`
--

CREATE TABLE `crime_category` (
  `categoryID` int(11) NOT NULL,
  `CrimeType` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crime_category`
--

INSERT INTO `crime_category` (`categoryID`, `CrimeType`, `description`) VALUES
(1, 'Illegal gamblings', 'Illegal gambling'),
(2, 'Rape', 'Rape'),
(3, 'theft', 'theft'),
(4, 'sex assaults', 'sex assaults');

-- --------------------------------------------------------

--
-- Table structure for table `crime_information`
--

CREATE TABLE `crime_information` (
  `crime_id` int(11) NOT NULL,
  `resident_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `dateTimeOfReport` datetime NOT NULL,
  `dateTimeOfIncident` datetime NOT NULL,
  `placeOfIncident` varchar(255) NOT NULL,
  `suspectName` varchar(255) NOT NULL,
  `statement` text NOT NULL,
  `qrcode` varchar(255) DEFAULT NULL,
  `evidencFilePath` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `resident_information`
--

CREATE TABLE `resident_information` (
  `resident_id` int(11) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `phoneNumber` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident_information`
--

INSERT INTO `resident_information` (`resident_id`, `firstName`, `lastName`, `dateOfBirth`, `address`, `phoneNumber`, `email`) VALUES
(1, 'Lowelljay', 'Brosoto', '1998-12-03', '4210 Bagong Tubig, San Luis Batangas', '0949869047', 'lowelljaybrosoto1998@gmail.com'),
(2, 'test', 'test', '2023-11-14', 'asdasdasdasdasdadasd', '1232123123123', 'lowelljaybrosoto1998@gmail.com'),
(3, 'officer', 'officer', '2023-11-15', 'asdadad', '1233', 'officer@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `roles` varchar(20) NOT NULL,
  `resident_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `roles`, `resident_id`) VALUES
(1, 'admin', '$2y$10$OiCFHdRo6ye4Q5w3TiguouS7Z3CXi17mFa.LKX1DsECcEtQZ.oMS.', 'admin@gmail.com', 'admin', 1),
(2, 'test', '$2y$10$rIqjsBCHNwzXZGhJtErKlewb696AY7kCsfSQsOJuMGfoGgUcTQf72', 'test@gmail.com', 'user', 2),
(3, 'officer', '$2y$10$V.aEKUgKKc82ly7FnWJ8JOEuFzmC7VwBW.MWhEWuzYl1QlHPARcGW', 'officer@gmail.com', 'officer', 3),
(4, 'test1', '$2y$10$b1p7tM9xs9OAZT24Euh60eDFOSCspf.mo8JulmycHx.98bT1R6UvS', 'test1@gmail.com', 'user', NULL),
(5, 'admin2', '$2y$10$ZrValpVMjBvVT/FLgYVv6eNvP9dbJCNk4KhNv.z/t0zdXjeCLEWE.', 'admin2@gmail.com', 'admin', NULL),
(6, 'test2', '$2y$10$Q3eoT31LQygkxhVE0HC6UeEqu2stgv1uag.dpPmZPDAbl39FuTagW', 'test2@gmail.com', 'user', NULL),
(7, 'test3', '$2y$10$qhbAhu1losDsZJlVs2ouR.DzVaX2NBXwPQLS5QcCwp0B7O7ATVdpq', 'test3@gmail.com', 'user', NULL),
(8, 'test4', '$2y$10$phlVqVtKPIm4CDpo53fl7u3yrUQZGxdPlzVT9Cf65v3IVp9cfPYbC', 'test4@gmail.com', 'officer', NULL),
(9, 'officer3', '$2y$10$vveiPsQT.GouEI1l6e.c7.jfEXuk7kQgTa0b5YlcSVbNEDyK9pH7y', 'asdad@gmail.com', 'admin', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crime_category`
--
ALTER TABLE `crime_category`
  ADD PRIMARY KEY (`categoryID`);

--
-- Indexes for table `crime_information`
--
ALTER TABLE `crime_information`
  ADD PRIMARY KEY (`crime_id`),
  ADD KEY `resident_id` (`resident_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resident_id` (`resident_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crime_category`
--
ALTER TABLE `crime_category`
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `crime_information`
--
ALTER TABLE `crime_information`
  MODIFY `crime_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resident_information`
--
ALTER TABLE `resident_information`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crime_information`
--
ALTER TABLE `crime_information`
  ADD CONSTRAINT `crime_information_ibfk_1` FOREIGN KEY (`resident_id`) REFERENCES `resident_information` (`resident_id`),
  ADD CONSTRAINT `crime_information_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `crime_category` (`categoryID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_resident` FOREIGN KEY (`resident_id`) REFERENCES `resident_information` (`resident_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
