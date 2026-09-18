-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2017 at 10:09 AM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `jobstep_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `idk_kontakt_info`
--

CREATE TABLE IF NOT EXISTS `idk_kontakt_info` (
  `kki_id` int(11) NOT NULL AUTO_INCREMENT,
  `kki_grupa` int(11) NOT NULL,
  `kki_naziv` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `kki_podatak` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `kki_kandidat_id` int(11) NOT NULL,
  PRIMARY KEY (`kki_id`),
  UNIQUE KEY `kki_id` (`kki_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `idk_kontakt_info`
--

INSERT INTO `idk_kontakt_info` (`kki_id`, `kki_grupa`, `kki_naziv`, `kki_podatak`, `kki_kandidat_id`) VALUES
(1, 2, 'Poslovni', 'Senad', 11),
(4, 1, 'Mobilni', '065888999', 11),
(5, 2, 'bojan@hotmail.com', NULL, 11),
(6, 1, 'mob', '+0089435786', 11);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
