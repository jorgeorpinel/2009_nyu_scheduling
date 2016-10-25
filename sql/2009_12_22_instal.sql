-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 22, 2009 at 02:18 PM
-- Server version: 5.0.82
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `scheduling`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` varchar(12) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Data is redundant due to section being part of id...';

--
-- Dumping data for table `courses`
--


-- --------------------------------------------------------

--
-- Table structure for table `courses_periods`
--

DROP TABLE IF EXISTS `courses_periods`;
CREATE TABLE `courses_periods` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` varchar(12) NOT NULL,
  `period_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_period` (`course_id`,`period_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `courses_periods`
--


-- --------------------------------------------------------

--
-- Table structure for table `courses_users`
--

DROP TABLE IF EXISTS `courses_users`;
CREATE TABLE `courses_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `course_id` varchar(12) NOT NULL,
  `user_id` varchar(9) NOT NULL,
  `credits` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_user` (`course_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `courses_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE `lessons` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `time_slot_id` int(11) unsigned NOT NULL,
  `student_id` varchar(9) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `timeslot_student` (`time_slot_id`,`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lessons`
--


-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(7) NOT NULL,
  `year` smallint(4) NOT NULL,
  `starts` date NOT NULL,
  `ends` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='type could be enum but cakephp doesnt support it.\nxx: year ?';

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`id`, `type`, `year`, `starts`, `ends`) VALUES
(1, 'Fall', 2009, '2009-08-21', '2009-12-22'),
(2, 'Summer', 2010, '2010-01-03', '2010-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `periods_students`
--

DROP TABLE IF EXISTS `periods_students`;
CREATE TABLE `periods_students` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `period_id` int(11) unsigned NOT NULL,
  `student_id` varchar(9) NOT NULL,
  `1st_login` tinyint(1) default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `period_user` (`period_id`,`student_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `periods_students`
--

INSERT INTO `periods_students` (`id`, `period_id`, `student_id`, `1st_login`) VALUES
(1, 1, 'N16176849', 0),
(2, 2, 'N16176849', 1);

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
CREATE TABLE `time_slots` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `faculty_id` varchar(9) NOT NULL,
  `course_id` varchar(12) NOT NULL,
  `day` char(1) default NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `time_avail` (`faculty_id`,`day`,`start_time`,`end_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `time_slots`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` varchar(9) NOT NULL,
  `password` varchar(32) default NULL,
  `type` varchar(7) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) default NULL,
  `telephone` varchar(255) default NULL,
  `status` char(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password`, `type`, `first_name`, `last_name`, `email`, `telephone`, `status`) VALUES
('N16176849', 'cd73502828457d15655bbd7a63fb0bc8', 'student', 'Jorge', 'Orpinel', 'jao327@nyu.edu', '(347) 569 0279', 'm'), -- pword: student
('Bill', NULL, 'admin', 'William', 'Naugle', 'bill.naugle@nyu.edu', '', NULL),
('Ingrid', NULL, 'admin', 'Ingrid', 'Green', 'ingrid.green@nyu.edu', '', NULL),
('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'System', 'Admin', NULL, NULL, NULL); -- pword: admin
