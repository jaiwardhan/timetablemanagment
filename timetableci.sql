-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 01, 2017 at 01:01 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 5.6.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timetableci`
--

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `course` varchar(30) NOT NULL,
  `day` varchar(10) NOT NULL,
  `schedule` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`course`, `day`, `schedule`) VALUES
('btech1', 'MON', '[\"CVIS\",\"CVIS\",\"MELAB\",\"MELAB\",\"HOLIDAY\",\"ED\",\"ED\",\"BCME\"]'),
('btech2', 'MON', '[\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\",\"ABC\"]'),
('barch1', 'MON', '[\"BARBCME\",\"BARBCME\",\"BARBCME\",\"BARBCME\",\"BARBCME\",\"BARBCME\",\"BARBCME\",\"BARBCME\"]'),
('btech1', 'TUE', '[\"MELAB\",\"BCME\",\"BCME\",\"BCME\",\"BCME\",\"BCME\",\"BCME\",\"BCME\"]');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(7) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `firstname`, `lastname`) VALUES
(1, 'jai@g.com', 'root', 'Master', 'Yoda');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
