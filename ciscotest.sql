-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 16, 2020 at 01:46 PM
-- Server version: 10.1.32-MariaDB
-- PHP Version: 5.6.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ciscotest`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblapiusers`
--

CREATE TABLE `tblapiusers` (
  `UserIDAPI` int(11) NOT NULL,
  `Username` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Password` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `APIKey` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` int(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tblapiusers`
--

INSERT INTO `tblapiusers` (`UserIDAPI`, `Username`, `Password`, `APIKey`, `Status`) VALUES
(1, 'admin', 'admin', 'admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblrouter`
--

CREATE TABLE `tblrouter` (
  `Rid` int(11) NOT NULL,
  `Sapid` varchar(18) DEFAULT NULL,
  `Hostname` varchar(14) DEFAULT NULL,
  `Loopback` varchar(15) DEFAULT NULL,
  `MacAddress` varchar(17) DEFAULT NULL,
  `Type` enum('AGI','CSS') DEFAULT NULL,
  `Status` tinyint(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tblrouter`
--

INSERT INTO `tblrouter` (`Rid`, `Sapid`, `Hostname`, `Loopback`, `MacAddress`, `Type`, `Status`) VALUES
(1, 'SAPID12345QWERTYUI', 'www.google.com', '192.205.1.125', 'zxcvbnmasdfghj', 'CSS', 2),
(2, 'SAPID12345QWERTWUI', 'www.yahoo.co.i', '192.205.1.158', 'qwertyuiopasdf', 'CSS', 2),
(3, 'SAPID54345QWERTYUI', 'TestHost.Com', '198.234.1.86', 'asdfghjklqwert', 'AGI', 2),
(5, 'SAP1234578', 'TestHost.Com12', '192.168.1.32', 'Test', 'AGI', 1),
(6, 'SAPID998999561', 'www.yahoo.in', '192.168.1.65', 'MAckakakkakkaka', 'CSS', 1),
(7, 'YwXNBqtjdzjfS9lydU', 'TGj3NEKjpSDTAm', '199.54.216.111', 'yvEyBosrngmtYlaDY', 'CSS', 1),
(8, 'kgoustH6r82Uv6nvnI', 'bYfPqVQLzrpnA7', '37.21.104.137', 'D1X3KwCPse4y00hP6', 'CSS', 1),
(9, 'SDAeopUy1UwjYXn6YT', 'X2eAdGtlC9V292', '237.115.112.112', 'i0hqN5YUaDGABtp3P', 'CSS', 1),
(10, 'XAp4Wce3KICZf7aALR', 'Y48TDaJJf6minz', '168.125.30.3', 'p59NG49vxHsVPXGLT', 'CSS', 1),
(11, '2h62WVihw9i1oFrKEG', 'zoSX2raFHR8xR8', '161.83.210.60', 'QOodUcDzqTrbYE8vD', 'CSS', 1),
(12, 'e5LBshdT5pVLdELcsL', '3FTXE86WS4cWCr', '237.251.201.86', 'MFbHUlqQdHGlMWO8b', 'CSS', 1),
(13, 'M9Ixsx2qFkzWEliG7X', 'GKh0GJROMI5eWi', '56.19.126.99', 'FCCzPOO7h8X4fs2Zr', 'CSS', 1),
(14, '7xY6XoCUj6172CLrsp', 'X5rscLkdyIXEEE', '198.197.120.69', 'EgyMhYSPbiH1daC4G', 'CSS', 1),
(15, '4KctYE7Tlq3O9GJzRw', 'lZrvDrmLOLBRAh', '136.49.244.149', 'CTcRntmPlGh3w6GQk', 'CSS', 1),
(16, 'RM4Mwi4jbsGUsYf1q4', 'DEFt9einFLTvvG', '213.52.226.67', 'z5wZUQ48ZZZ5rfBjj', 'CSS', 1),
(17, 'PMNfrQZilES4cxUFhm', '4FgsPboE8NoktB', '52.66.156.10', 'riObjUCFb0RBMX07l', 'CSS', 1),
(18, 'z2NzcoJGI4woqwfWbI', 'v2l8pTKJweJZXh', '230.227.206.169', '9RjJkPFP8RCiTqhks', 'CSS', 1),
(19, 'udEavwuRtOzTQb56zc', 'MP9YRVx63JdGz6', '110.244.181.205', '8h8mL1Xl5uLqzLMra', 'CSS', 1),
(20, 'sHIlFL66XHXSsH7EJu', 'VTTidAwQlaJk8C', '30.81.252.18', '6vYqIyEwbdypBHv3y', 'CSS', 1),
(21, 'JRU8wrWWNCD4MdX6aB', 'm9cAPNBwP8zhkK', '229.116.127.103', 'PaNLC3bJChZM5GauI', 'CSS', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblapiusers`
--
ALTER TABLE `tblapiusers`
  ADD PRIMARY KEY (`UserIDAPI`);

--
-- Indexes for table `tblrouter`
--
ALTER TABLE `tblrouter`
  ADD PRIMARY KEY (`Rid`),
  ADD UNIQUE KEY `Sapid` (`Sapid`),
  ADD UNIQUE KEY `Hostname` (`Hostname`),
  ADD UNIQUE KEY `Loopback` (`Loopback`),
  ADD UNIQUE KEY `MacAddress` (`MacAddress`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblapiusers`
--
ALTER TABLE `tblapiusers`
  MODIFY `UserIDAPI` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblrouter`
--
ALTER TABLE `tblrouter`
  MODIFY `Rid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
