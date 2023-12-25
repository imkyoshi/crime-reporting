-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2023 at 07:28 PM
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
-- Table structure for table `crime_information`
--

CREATE TABLE `crime_information` (
  `crime_id` int(11) NOT NULL,
  `dateTimeOfReport` datetime NOT NULL,
  `dateTimeOfIncident` datetime NOT NULL,
  `placeOfIncident` varchar(255) NOT NULL,
  `suspectName` varchar(255) NOT NULL,
  `statement` text NOT NULL,
  `qrcode` varchar(255) NOT NULL,
  `formFileEvidence` varchar(255) DEFAULT NULL,
  `status` enum('Pending','UnderInvestigation','Confirmed') NOT NULL,
  `formFileValidID` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `CrimeType` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crime_information`
--

INSERT INTO `crime_information` (`crime_id`, `dateTimeOfReport`, `dateTimeOfIncident`, `placeOfIncident`, `suspectName`, `statement`, `qrcode`, `formFileEvidence`, `status`, `formFileValidID`, `email`, `CrimeType`) VALUES
(1, '2023-12-08 11:26:00', '2023-12-08 05:26:00', 'bagong tubig', 'asdasdasda', 'asdasdasd', '', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\evidences\\385534297_751435516794520_6378776646762818244_n.png', 'Pending', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\valid_ids\\370194089_884899536320036_3984942559530862056_n.jpg', 'test@gmail.com', 'Illegal gambling'),
(2, '2023-12-07 11:28:00', '2023-12-07 07:28:00', 'Dulangan', 'luziel', 'asdasdadsads', '', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\evidences\\2.jpg', 'Pending', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\valid_ids\\1.jpg', 'test@gmail.com', 'Rape'),
(3, '2023-12-06 11:29:00', '2023-12-06 15:29:00', 'poblacion', 'earl', 'asdadsasd', '', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\evidences\\4.jpg', 'Pending', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\valid_ids\\3.jpg', 'test@gmail.com', 'theft'),
(4, '2023-12-05 11:31:00', '2023-12-05 16:31:00', 'banoyo', 'jan lois', 'asdasdasd', '', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\evidences\\4.jpg', 'Pending', 'C:\\xampp\\htdocs\\crime-reporting\\view\\dist\\uploads\\valid_ids\\5.jpg', 'test@gmail.com', 'sex assault'),
(5, '2023-12-23 14:23:00', '2023-12-23 16:23:00', 'Bagong Tubig, San Luis, 4210, PH', 'vincents', 'asdasdasdasdsad', '', 'C:\\xampp\\htdocs\\crime-reporting\\admin\\dist\\uploads\\evidences\\385534297_751435516794520_6378776646762818244_n.png', 'UnderInvestigation', 'C:\\xampp\\htdocs\\crime-reporting\\admin\\dist\\uploads\\valid_ids\\370194089_884899536320036_3984942559530862056_n.jpg', 'test1@gmail.com', 'Rape'),
(6, '2023-12-23 16:34:00', '2023-12-23 20:34:00', 'asdasdasdasdasda', 'kkkkkkkkkkk', 'asdasdasdasd', '', 'C:/xampp/htdocs/crime-reporting/admin/dist/uploads/evidences/370194089_884899536320036_3984942559530862056_n.jpg', 'Pending', 'C:/xampp/htdocs/crime-reporting/admin/dist/uploads/valid_ids/385534297_751435516794520_6378776646762818244_n.png', 'lowelljay_brosoto@yahoo.com', 'Rape');

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

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crime_information`
--
ALTER TABLE `crime_information`
  ADD PRIMARY KEY (`crime_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crime_information`
--
ALTER TABLE `crime_information`
  MODIFY `crime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
