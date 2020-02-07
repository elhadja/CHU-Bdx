-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 03, 2019 at 04:11 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tmandallena`
--

-- --------------------------------------------------------

--
-- Table structure for table `mattress`
--

CREATE TABLE IF NOT EXISTS `mattress` (
  `ID` bigint(20) unsigned NOT NULL auto_increment,
  `NAME` text collate utf8_bin NOT NULL,
  `ID_TYPE` int(11) NOT NULL,
  `ID_STATE` int(11) NOT NULL,
  `URGENT` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=48 ;

--
-- Dumping data for table `mattress`
--

INSERT INTO `mattress` (`ID`, `NAME`, `ID_TYPE`, `ID_STATE`, `URGENT`) VALUES
(38, 'M9', 1, 2, 0),
(37, 'M10', 3, 1, 0),
(36, 'M11', 2, 1, 0),
(35, 'M12', 1, 1, 0),
(39, 'M8', 1, 2, 0),
(40, 'M7', 2, 2, 0),
(41, 'M6', 3, 2, 0),
(42, 'M5', 1, 3, 0),
(43, 'M4', 2, 3, 0),
(44, 'M3', 3, 3, 0),
(45, 'M2', 1, 1, 1),
(46, 'M1', 2, 1, 1),
(47, 'M0', 3, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `mattress_state`
--

CREATE TABLE IF NOT EXISTS `mattress_state` (
  `ID` int(11) NOT NULL auto_increment,
  `NAME` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `mattress_state`
--

INSERT INTO `mattress_state` (`ID`, `NAME`) VALUES
(1, 'Bon'),
(2, 'A remplacer'),
(3, 'Ne pas utiliser');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE IF NOT EXISTS `patients` (
  `ID` int(11) NOT NULL auto_increment,
  `NAME` text collate utf8_bin NOT NULL,
  `SURNAME` text collate utf8_bin NOT NULL,
  `NIP` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`ID`, `NAME`, `SURNAME`, `NIP`) VALUES
(23, 'Camille', 'Bob', 658924562),
(22, 'Marie', 'Celine', 456785421),
(21, 'Jack', 'Fred', 687456852),
(20, 'Paul', 'Uber', 456321789),
(19, 'Jean', 'Charles', 123456789);

-- --------------------------------------------------------

--
-- Table structure for table `rdv`
--

CREATE TABLE IF NOT EXISTS `rdv` (
  `ID` int(11) NOT NULL auto_increment,
  `DATE_SCANNER` date NOT NULL,
  `DATE_MEP` date NOT NULL,
  `DATE_FTR` date NOT NULL,
  `NB_SESSION` text collate utf8_bin NOT NULL,
  `NOTE` text collate utf8_bin NOT NULL,
  `ID_MATTRESS` int(11) NOT NULL,
  `ID_USER` int(11) NOT NULL,
  `ID_STATE` int(11) NOT NULL,
  `ID_PATIENT` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=92 ;

--
-- Dumping data for table `rdv`
--

INSERT INTO `rdv` (`ID`, `DATE_SCANNER`, `DATE_MEP`, `DATE_FTR`, `NB_SESSION`, `NOTE`, `ID_MATTRESS`, `ID_USER`, `ID_STATE`, `ID_PATIENT`) VALUES
(91, '2019-05-07', '2019-05-07', '2019-06-27', 'S4654s', '', 40, 2, 1, 19),
(90, '2019-05-07', '2019-05-07', '2019-05-17', '12', '', 36, 2, 1, 19),
(89, '2019-06-15', '2019-06-15', '2019-07-03', '51', '', 39, 2, 1, 23),
(88, '2019-05-24', '2019-05-24', '2019-07-19', '9S', '', 35, 3, 1, 22),
(87, '2019-05-22', '2019-05-22', '2019-06-14', '8S', '', 38, 2, 1, 19),
(86, '2019-05-07', '2019-05-07', '2019-06-10', '4S', '', 39, 2, 1, 21),
(84, '2019-05-07', '2019-05-07', '2019-05-17', '5S', '', 38, 2, 1, 19),
(85, '2019-05-07', '2019-05-07', '2019-05-19', '5S', '', 35, 2, 1, 20);

-- --------------------------------------------------------

--
-- Table structure for table `rdv_state`
--

CREATE TABLE IF NOT EXISTS `rdv_state` (
  `ID` int(11) NOT NULL auto_increment,
  `NAME` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `rdv_state`
--

INSERT INTO `rdv_state` (`ID`, `NAME`) VALUES
(1, 'Normal'),
(2, 'Reporté'),
(3, 'Annulé');

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE IF NOT EXISTS `type` (
  `ID` int(11) NOT NULL auto_increment,
  `NAME` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=4 ;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`ID`, `NAME`) VALUES
(1, 'Crane'),
(2, 'Thorax'),
(3, 'Abdomen');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL auto_increment,
  `USERNAME` varchar(60) collate utf8_bin NOT NULL,
  `MATRICULE` varchar(125) collate utf8_bin NOT NULL,
  `PWD` varchar(512) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `USERNAME`, `MATRICULE`, `PWD`) VALUES
(2, 'Admin', '0123456789', '$2y$12$0Gs.Gp3wlC/p3iCW/DCLyOnZW3yps1cMgFoEsNYll5HGiIHG8H5su'),
(3, 'Thibaut', '12345', '$2y$12$aUpFsHLJRpDeiPCYhZocmOUgdYTiVXStc6KD0/OFmjr4pViCqw4vi'),
(4, 'Admin', '', '$2y$12$SDrlfrhCElhqXif6hiL1NuJyHCx20/BESHLM/.UPv/OQ3RpueqsNy'),
(5, 'Admin', '', '$2y$12$A08nkeXrX5c6.S.PYhmLpOUqLYQ65bYkXG87gwDook1CAVCH8Oq0S'),
(6, 'Admin', '', '$2y$12$Iobjckx6pFq32ksZKpkxGOzz/XRyhNMXFCf.gsqUJhBZu1CNeY6se'),
(7, 'Admin', '', '$2y$12$/WkSiXsDkKjARQ.ZhnTOGuYI/BcUmYAQIAiiEFIFL0zw3q88mmfee');

-- --------------------------------------------------------

--
-- Table structure for table `users_online`
--

CREATE TABLE IF NOT EXISTS `users_online` (
  `ID` int(11) NOT NULL auto_increment,
  `TIME` datetime NOT NULL,
  `ID_USER` int(11) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=118 ;

--
-- Dumping data for table `users_online`
--


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
