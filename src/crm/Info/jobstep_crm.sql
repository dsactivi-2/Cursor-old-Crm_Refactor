-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2017 at 10:27 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jobstep_crm`
--

-- --------------------------------------------------------

--
-- Table structure for table `idk_documents`
--

CREATE TABLE `idk_documents` (
  `document_id` int(11) NOT NULL,
  `document_name` varchar(150) NOT NULL,
  `document_desc` varchar(250) NOT NULL,
  `document_file` varchar(150) NOT NULL,
  `document_icon` varchar(5) NOT NULL,
  `document_datetime` datetime NOT NULL,
  `document_group` int(2) NOT NULL,
  `document_dataid` int(11) NOT NULL,
  `document_employeeid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_documents`
--

INSERT INTO `idk_documents` (`document_id`, `document_name`, `document_desc`, `document_file`, `document_icon`, `document_datetime`, `document_group`, `document_dataid`, `document_employeeid`) VALUES
(1, 'Test', 'test', '59902f50b0bcd.txt', 'txt', '2017-08-13 12:52:00', 1, 1, 1),
(4, 'Test', 'test', '59903acb362ac.doc', 'doc', '2017-08-13 13:40:59', 1, 1, 1),
(5, 'Novi dokument za test', 'Test dokument ono sto jeste', '5990464dd6f27.doc', 'doc', '2017-08-13 14:30:05', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `idk_employees`
--

CREATE TABLE `idk_employees` (
  `employee_id` int(11) NOT NULL,
  `employee_firstname` varchar(150) NOT NULL,
  `employee_lastname` varchar(150) NOT NULL,
  `employee_email` varchar(250) NOT NULL,
  `employee_password` varchar(250) NOT NULL,
  `employee_jmbg` bigint(20) DEFAULT NULL,
  `employee_rfid` varchar(50) NOT NULL,
  `employee_position` varchar(150) NOT NULL,
  `employee_dob` date DEFAULT NULL,
  `employee_doe` date DEFAULT NULL,
  `employee_phone` varchar(50) NOT NULL,
  `employee_address` text NOT NULL,
  `employee_city` varchar(150) NOT NULL,
  `employee_country` varchar(150) NOT NULL,
  `employee_info` text NOT NULL,
  `employee_inote` text NOT NULL,
  `employee_image` varchar(150) NOT NULL,
  `employee_status` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_employees`
--

INSERT INTO `idk_employees` (`employee_id`, `employee_firstname`, `employee_lastname`, `employee_email`, `employee_password`, `employee_jmbg`, `employee_rfid`, `employee_position`, `employee_dob`, `employee_doe`, `employee_phone`, `employee_address`, `employee_city`, `employee_country`, `employee_info`, `employee_inote`, `employee_image`, `employee_status`) VALUES
(1, 'Dino', 'Kišmić', 'dino.kismic@idkstudio.com', 'b246ff693d453c3b1a3049752da2bc75', 0, '', '', '1983-09-11', '2014-08-01', '', '', '', '', '', 'testfsdafdsa123', 'none.jpg', 1),
(2, 'Denis', 'Selmanovic', 'denis@jobstep.ba', 'c3875d07f44c422f3b3bc019c23e16ae', 0, '', '', '2017-08-07', '2017-08-01', '', '', '', '', '', '', 'none.jpg', 1),
(3, 'Elvis', 'Delic Ibukic', 'elvis@elsiv.com', '8b28c7134887bb938e1ffed68456ffb2', 0, '', '', '1970-01-01', '1970-01-01', '', '', '', '', '', '', 'none', 2),
(5, 'Vesmir', 'Ruznic', 'vesmir@vesmir.com', '843ac03cece6845d599673e46177d6df', 123654987456, '', '', '2017-08-08', '2017-08-09', '06111111', 'Repusine bb', 'Bihać', 'BIH', '', '', 'none', 1);

-- --------------------------------------------------------

--
-- Table structure for table `idk_kandidati`
--

CREATE TABLE `idk_kandidati` (
  `kandidat_id` int(11) NOT NULL,
  `kandidat_ime` varchar(150) NOT NULL,
  `kandidat_prezime` varchar(150) NOT NULL,
  `kandidat_spol` varchar(10) NOT NULL,
  `kandidat_djevojackoprezime` varchar(150) NOT NULL,
  `kandidat_jmbg` int(20) NOT NULL,
  `kandidat_brojlk` varchar(50) NOT NULL,
  `kandidat_datumrodjenja` date NOT NULL,
  `kandidat_mjestorodjenja` varchar(150) NOT NULL,
  `kandidat_drzavarodjenja` varchar(150) NOT NULL,
  `kandidat_drzavljanstvo` varchar(150) NOT NULL,
  `kandidat_bracnostanje` varchar(50) NOT NULL,
  `kandidat_bracnostanjeod` date NOT NULL,
  `kandidat_adresa` text NOT NULL,
  `kandidat_grad` varchar(150) NOT NULL,
  `kandidat_pbroj` int(20) NOT NULL,
  `kandidat_drzava` varchar(150) NOT NULL,
  `kandidat_email` varchar(250) NOT NULL,
  `kandidat_password` varchar(150) NOT NULL,
  `kandidat_slika` varchar(150) NOT NULL,
  `kandidat_status` int(5) NOT NULL,
  `kandidat_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_kandidati`
--

INSERT INTO `idk_kandidati` (`kandidat_id`, `kandidat_ime`, `kandidat_prezime`, `kandidat_spol`, `kandidat_djevojackoprezime`, `kandidat_jmbg`, `kandidat_brojlk`, `kandidat_datumrodjenja`, `kandidat_mjestorodjenja`, `kandidat_drzavarodjenja`, `kandidat_drzavljanstvo`, `kandidat_bracnostanje`, `kandidat_bracnostanjeod`, `kandidat_adresa`, `kandidat_grad`, `kandidat_pbroj`, `kandidat_drzava`, `kandidat_email`, `kandidat_password`, `kandidat_slika`, `kandidat_status`, `kandidat_datetime`) VALUES
(1, 'Dino', 'Kismic', 'Muško', '', 2147483647, 'ft56gr', '0000-00-00', 'Bihac', 'BiH', 'BiH', 'Oženjen / Udana', '2014-08-16', 'Repusine bb', 'Bihac', 77000, 'BiH', 'dino.kismic@idkstudio.com', 'b246ff693d453c3b1a3049752da2bc75', '5993fb85e4223.jpg', 0, '2017-08-16 10:00:05');

-- --------------------------------------------------------

--
-- Table structure for table `idk_logs`
--

CREATE TABLE `idk_logs` (
  `log_id` int(11) NOT NULL,
  `log_employeeid` int(11) NOT NULL,
  `log_desc` varchar(250) NOT NULL,
  `log_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_logs`
--

INSERT INTO `idk_logs` (`log_id`, `log_employeeid`, `log_desc`, `log_date`) VALUES
(1, 1, 'Uredio profil zaposlenika: Dino Kišmić ', '2017-08-13 12:51:40'),
(2, 1, 'Dodao novi dokument: Test ', '2017-08-13 12:52:00'),
(3, 1, 'Snimio važne napomene za zaposlenika: Dino Kišmić ', '2017-08-13 12:52:10'),
(4, 1, 'Obrisao bilješku: Test ', '2017-08-13 13:01:20'),
(5, 1, 'Obrisao bilješku: test123 ', '2017-08-13 13:02:56'),
(6, 1, 'Dodao novog zaposlenika: Denis Selmanovic ', '2017-08-13 13:03:57'),
(7, 1, 'Uredio profil zaposlenika: Denis Selmanovic ', '2017-08-13 13:04:58'),
(8, 1, 'Uredio profil zaposlenika: Denis Selmanovic ', '2017-08-13 13:05:22'),
(9, 1, 'Arhivirao zaposlenika: Denis Selmanovic ', '2017-08-13 13:05:33'),
(10, 1, 'Uredio profil zaposlenika: Denis Selmanovic ', '2017-08-13 13:05:59'),
(11, 1, 'Arhivirao zaposlenika: Denis Selmanovic ', '2017-08-13 13:06:02'),
(12, 1, 'Uredio profil zaposlenika: Denis Selmanovic ', '2017-08-13 13:06:11'),
(13, 1, 'Obrisao bilješku: hhh ', '2017-08-13 13:06:19'),
(14, 1, 'Dodao novi dokument: Test ', '2017-08-13 13:06:25'),
(15, 1, 'Obrisao bilješku: tfttg ', '2017-08-13 13:11:52'),
(16, 1, 'Obrisao dokument: Test ', '2017-08-13 13:20:02'),
(17, 1, 'Obrisao dokument:  ', '2017-08-13 13:39:56'),
(18, 1, 'Dodao novi dokument: tydfsa ', '2017-08-13 13:40:24'),
(19, 1, 'Obrisao dokument: tydfsa ', '2017-08-13 13:40:30'),
(20, 1, 'Obrisao bilješku: test ', '2017-08-13 13:40:37'),
(21, 1, 'Dodao novi dokument: Test ', '2017-08-13 13:40:59'),
(22, 1, 'Snimio važne napomene za zaposlenika: Dino Kišmić ', '2017-08-13 13:42:20'),
(23, 1, 'Snimio važne napomene za zaposlenika: Dino Kišmić ', '2017-08-13 13:44:30'),
(24, 1, 'Dodao novog zaposlenika: Elvis Delic Ibukic ', '2017-08-13 14:04:22'),
(25, 1, 'Dodao novog zaposlenika: Vesmir Ruznic ', '2017-08-13 14:07:44'),
(26, 1, 'Dodao novog zaposlenika: Vesmir Ruznic ', '2017-08-13 14:10:04'),
(27, 1, 'Dodao novi dokument: Novi dokument za test ', '2017-08-13 14:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `idk_notes`
--

CREATE TABLE `idk_notes` (
  `note_id` int(11) NOT NULL,
  `note_txt` text NOT NULL,
  `note_datetime` datetime NOT NULL,
  `note_group` int(2) NOT NULL,
  `note_dataid` int(11) NOT NULL,
  `note_employeeid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_notes`
--

INSERT INTO `idk_notes` (`note_id`, `note_txt`, `note_datetime`, `note_group`, `note_dataid`, `note_employeeid`) VALUES
(3, 'test321', '2017-08-13 13:03:01', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `idk_otherdata`
--

CREATE TABLE `idk_otherdata` (
  `otherdata_id` int(11) NOT NULL,
  `otherdata_data` varchar(150) NOT NULL,
  `otherdata_group` int(5) NOT NULL,
  `otherdata_dataid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `idk_search`
--

CREATE TABLE `idk_search` (
  `search_id` int(11) NOT NULL,
  `search_text` varchar(100) NOT NULL,
  `search_tags` text NOT NULL,
  `search_link` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idk_search`
--

INSERT INTO `idk_search` (`search_id`, `search_text`, `search_tags`, `search_link`) VALUES
(1, 'Zaposlenik: Elvis Delic Ibukic ', 'Elvis Delic Ibukic', 'employees?page=open&id=3'),
(3, 'Zaposlenik: Vesmir Ruznic ', 'Vesmir Ruznic 123654987456 vesmir@vesmir.com 2017-08-08 2017-08-09 06111111 Repusine bb Bihać BIH', 'employees?page=open&id=5'),
(4, 'Dokument: Novi dokument za test', 'Novi dokument za test Test dokument ono sto jeste', 'files/employees/5990464dd6f27.doc');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `idk_documents`
--
ALTER TABLE `idk_documents`
  ADD PRIMARY KEY (`document_id`);

--
-- Indexes for table `idk_employees`
--
ALTER TABLE `idk_employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `idk_kandidati`
--
ALTER TABLE `idk_kandidati`
  ADD PRIMARY KEY (`kandidat_id`);

--
-- Indexes for table `idk_logs`
--
ALTER TABLE `idk_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `idk_notes`
--
ALTER TABLE `idk_notes`
  ADD PRIMARY KEY (`note_id`);

--
-- Indexes for table `idk_otherdata`
--
ALTER TABLE `idk_otherdata`
  ADD PRIMARY KEY (`otherdata_id`);

--
-- Indexes for table `idk_search`
--
ALTER TABLE `idk_search`
  ADD PRIMARY KEY (`search_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `idk_documents`
--
ALTER TABLE `idk_documents`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `idk_employees`
--
ALTER TABLE `idk_employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `idk_kandidati`
--
ALTER TABLE `idk_kandidati`
  MODIFY `kandidat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `idk_logs`
--
ALTER TABLE `idk_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `idk_notes`
--
ALTER TABLE `idk_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `idk_otherdata`
--
ALTER TABLE `idk_otherdata`
  MODIFY `otherdata_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `idk_search`
--
ALTER TABLE `idk_search`
  MODIFY `search_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
