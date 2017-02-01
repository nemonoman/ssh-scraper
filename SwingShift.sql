-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 01, 2017 at 05:01 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `SwingShift`
--

-- --------------------------------------------------------

--
-- Table structure for table `Hotels`
--

CREATE TABLE `Hotels` (
  `id` int(5) NOT NULL auto_increment,
  `ssid` varchar(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `Hotels`
--

INSERT INTO `Hotels` VALUES(1, 'F7KuzMS1qm', 'Atton Brickell Miami Hotel', 'Miami', 'FL');
INSERT INTO `Hotels` VALUES(2, 'bEPPAu2Xjj', 'Avanti Resort Orlando', 'Orlando', 'FL');
INSERT INTO `Hotels` VALUES(3, 'rAreQvvdKa', 'Buena Vista Palace', 'Lake Buena Vista', 'FL');
INSERT INTO `Hotels` VALUES(4, 'U4B9Du3bAt', 'Carillon Miami Beach', 'Miami Beach', 'FL');
INSERT INTO `Hotels` VALUES(5, 'nDo6j6EQFC', 'Duane Street Hotel', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(6, '04DevQPTmY', 'Faena Hotel Miami Beach', 'Miami Beach', 'FL');
INSERT INTO `Hotels` VALUES(7, 'ypHxOvhRBl', 'Fontainebleau Miami Beach', 'Miami Beach', 'FL');
INSERT INTO `Hotels` VALUES(8, 'Xyg9R3JSzn', 'Hilton Garden Inn Queens/JFK Airport', 'Jamaica', 'NY');
INSERT INTO `Hotels` VALUES(9, 'VMc1OmBabO', 'Hilton Orlando Lake Buena Vista', 'Lake Buena Vista', 'FL');
INSERT INTO `Hotels` VALUES(10, 'eHIExZ7Tmh', 'Hotel 48LEX', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(11, '2N101E3XwC', 'Hotel Plaza Athenee', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(12, 'iwDLD5WoHv', 'Intercontinental Miami', 'Miami', 'FL');
INSERT INTO `Hotels` VALUES(13, 'rDYxotBzqX', 'InterContinental New York Barclay', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(14, 'lRQknpjIIw', 'Mondrian South Beach Hotel', 'South Beach', 'FL');
INSERT INTO `Hotels` VALUES(15, 'xD66dYgxr0', 'Mosaic Hotel', 'Beverly Hills', 'CA');
INSERT INTO `Hotels` VALUES(16, '83TpwGfuZy', 'Pelican Grand Beach Resort', 'Fort Lauderdale', 'FL');
INSERT INTO `Hotels` VALUES(17, 'XI9luNTJYw', 'PGA National Resort & Spa', 'Palm Beach Gardens', 'FL');
INSERT INTO `Hotels` VALUES(18, 'XsvGGho3EN', 'Plunge Beach Hotel', 'Lauderdale by the Sea', 'FL');
INSERT INTO `Hotels` VALUES(19, 'UpRuGw6E4W', 'Royal Palm South Beach Miami', 'South Beach', 'FL');
INSERT INTO `Hotels` VALUES(20, 'xOW8KxwEmk', 'Sheraton JFK Airport Hotel', 'Jamaica', 'NY');
INSERT INTO `Hotels` VALUES(21, '2UH7t5LxUw', 'The Langford Hotel', 'Miami', 'FL');
INSERT INTO `Hotels` VALUES(22, 'LSLnTi38jA', 'The Out Hotel', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(23, '5DVW6OtyVN', 'The Plaza', 'New York', 'NY');
INSERT INTO `Hotels` VALUES(24, 'bHb7PKEb28', 'The Westin Fort Lauderdale Beach Resort', 'Fort Lauderdale', 'FL');

-- --------------------------------------------------------

--
-- Table structure for table `SiteHotels`
--

CREATE TABLE `SiteHotels` (
  `siteid` int(5) NOT NULL,
  `hotelid` int(5) NOT NULL,
  `lookup1` varchar(200) NOT NULL,
  `lookup2` varchar(200) NOT NULL,
  `lookup3` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SiteHotels`
--

INSERT INTO `SiteHotels` VALUES(2, 1, 'us', 'atton-brickell-miami.html', '20023181');
INSERT INTO `SiteHotels` VALUES(1, 1, '565892', '', '');
INSERT INTO `SiteHotels` VALUES(2, 10, 'us', 'new-york-517-lexington-avenue.html', '20088325');
INSERT INTO `SiteHotels` VALUES(1, 10, '374895', '', '');
INSERT INTO `SiteHotels` VALUES(2, 2, 'us', 'avanti-resort.html', '20023488');
INSERT INTO `SiteHotels` VALUES(1, 2, '193986', '', '');
INSERT INTO `SiteHotels` VALUES(1, 3, '182220', '', '');
INSERT INTO `SiteHotels` VALUES(1, 4, '537635', '', '');
INSERT INTO `SiteHotels` VALUES(1, 5, '260426', '', '');
INSERT INTO `SiteHotels` VALUES(1, 6, '332026112', '', '');
INSERT INTO `SiteHotels` VALUES(1, 7, '239180', '', '');
INSERT INTO `SiteHotels` VALUES(1, 8, '223947', '', '');
INSERT INTO `SiteHotels` VALUES(1, 9, '112876', '', '');
INSERT INTO `SiteHotels` VALUES(1, 11, '113922', '', '');
INSERT INTO `SiteHotels` VALUES(1, 12, '106250', '', '');
INSERT INTO `SiteHotels` VALUES(1, 13, '106208', '', '');
INSERT INTO `SiteHotels` VALUES(1, 14, '272254', '', '');
INSERT INTO `SiteHotels` VALUES(1, 15, '199833', '', '');
INSERT INTO `SiteHotels` VALUES(1, 16, '184099', '', '');
INSERT INTO `SiteHotels` VALUES(1, 17, '199093', '', '');
INSERT INTO `SiteHotels` VALUES(1, 18, '591821', '', '');
INSERT INTO `SiteHotels` VALUES(1, 19, '395969', '', '');
INSERT INTO `SiteHotels` VALUES(1, 20, '275408', '', '');
INSERT INTO `SiteHotels` VALUES(1, 21, '554465', '', '');
INSERT INTO `SiteHotels` VALUES(1, 22, '402509', '', '');
INSERT INTO `SiteHotels` VALUES(1, 23, '105314', '', '');
INSERT INTO `SiteHotels` VALUES(1, 24, '105812', '', '');
INSERT INTO `SiteHotels` VALUES(2, 3, 'us', 'buena-vista-palace-spa.html', '20023488');
INSERT INTO `SiteHotels` VALUES(2, 4, 'us', 'carillon-miami-beach.html', '20023182');
INSERT INTO `SiteHotels` VALUES(2, 5, 'us', 'duane-street.html', '20088325');
INSERT INTO `SiteHotels` VALUES(2, 6, 'us', 'faena-miami-beach.html', '20023182');
INSERT INTO `SiteHotels` VALUES(2, 7, 'us', 'fontainebleau-resort.html', '20023182');
INSERT INTO `SiteHotels` VALUES(2, 14, 'us', 'mondrian-miami-beach.html', '20023182');

-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE `Sites` (
  `id` int(5) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `baseurl` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `Sites`
--

INSERT INTO `Sites` VALUES(1, 'Hotels.com', 'www.hotels.com');
INSERT INTO `Sites` VALUES(2, 'Booking.com', 'www.booking.com');
