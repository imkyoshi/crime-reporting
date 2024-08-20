-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2024 at 06:06 PM
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
-- Database: `crime-app`
--

-- --------------------------------------------------------

--
-- Table structure for table `crime_category`
--

CREATE TABLE `crime_category` (
  `categoryID` int(11) NOT NULL,
  `crimeName` varchar(255) NOT NULL,
  `crimeType` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crime_information`
--

CREATE TABLE `crime_information` (
  `crime_id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `formFileValidID` varchar(255) DEFAULT NULL,
  `dateTimeOfReport` datetime NOT NULL,
  `dateTimeOfIncident` datetime NOT NULL,
  `placeOfIncident` varchar(255) NOT NULL,
  `suspectName` varchar(255) NOT NULL,
  `statement` text NOT NULL,
  `formFileEvidence` varchar(255) DEFAULT NULL,
  `CrimeType` varchar(255) DEFAULT NULL,
  `qrcode` varchar(255) NOT NULL,
  `status` enum('Pending','UnderInvestigation','Confirmed') NOT NULL,
  `dateCreated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `crime_information`
--
DELIMITER $$
CREATE TRIGGER `before_insert_crime_information` BEFORE INSERT ON `crime_information` FOR EACH ROW BEGIN
    DECLARE crime_counter INT;

    -- Find the next available crime_counter
    SELECT IFNULL(MAX(CAST(SUBSTRING(crime_id, 5) AS SIGNED)), 0) + 1 INTO crime_counter
    FROM crime_information;

    -- Set the new crime_id in the specified format
    SET NEW.crime_id = CONCAT('CRS-', crime_counter);
END
$$
DELIMITER ;

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

-- --------------------------------------------------------

--
-- Table structure for table `suspect_information`
--

CREATE TABLE `suspect_information` (
  `SuspectID` int(11) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `DateOfBirth` varchar(255) NOT NULL,
  `Gender` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Nationality` varchar(255) NOT NULL,
  `qrcode` varchar(255) NOT NULL,
  `dateCreated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `phoneNumber` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `dateOfBirth` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `roles` varchar(20) NOT NULL,
  `resident_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD PRIMARY KEY (`crime_id`);

--
-- Indexes for table `resident_information`
--
ALTER TABLE `resident_information`
  ADD PRIMARY KEY (`resident_id`);

--
-- Indexes for table `suspect_information`
--
ALTER TABLE `suspect_information`
  ADD PRIMARY KEY (`SuspectID`);

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
  MODIFY `categoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `crime_information`
--
ALTER TABLE `crime_information`
  MODIFY `crime_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `resident_information`
--
ALTER TABLE `resident_information`
  MODIFY `resident_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suspect_information`
--
ALTER TABLE `suspect_information`
  MODIFY `SuspectID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
