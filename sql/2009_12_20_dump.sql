-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 20, 2009 at 11:58 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

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
CREATE TABLE IF NOT EXISTS `courses` (
  `id` varchar(12) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Data is redundant due to section being part of id...';

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`) VALUES
('E85.2021.001', 'MUSIC REFERENCE AND'),
('E85.2042.001', 'PSYCHOLOGY OF MUSIC'),
('E85.2501.001', 'ETHICS OF ENTERTAINMENT'),
('E85.2512.001', 'CONCERT MANAGEMENT'),
('E85.2517.001', 'MUSIC BUSINESS GRADUATE'),
('E03.0002.001', 'SCHOLARS SEMINAR'),
('E89.1076.003', 'ADVANCED DANCE PRACTICUM'),
('E85.1201.001', 'MUSIC FOR CHILDREN'),
('E85.0056.038', 'PIANO (PRIVATE LESSONS)'),
('E17.2030.001', 'DRAMATIC ACTIVITIES IN'),
('E47.4747.001', 'MAINTAIN MATRICULATION'),
('E85.0036.005', 'MUSIC THEORY II'),
('E85.2939.001', 'COLLOQUY IN MUSIC ED'),
('E85.0063.019', 'VOCAL TRAINING (PRIVATE'),
('E85.0092.010', 'COLLEGIUM & PROGRAM SEM'),
('E85.1034.023', 'WIND/PERCUSSION INSTRMNT'),
('E85.1078.001', 'MUSIC HIST IV: TWENTIETH'),
('E85.1080.018', 'CHAMBER ENSEMBLES'),
('E17.1174.001', 'STUD TEACH:THEATRE IN'),
('E17.2953.001', 'THE TEACHING ARTIST');

-- --------------------------------------------------------

--
-- Table structure for table `courses_periods`
--

DROP TABLE IF EXISTS `courses_periods`;
CREATE TABLE IF NOT EXISTS `courses_periods` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` varchar(12) NOT NULL,
  `period_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_period` (`course_id`,`period_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Dumping data for table `courses_periods`
--

INSERT INTO `courses_periods` (`id`, `course_id`, `period_id`) VALUES
(1, 'E85.2021.001', 1),
(2, 'E85.2042.001', 1),
(3, 'E85.2501.001', 1),
(4, 'E85.2512.001', 1),
(5, 'E85.2517.001', 1),
(6, 'E03.0002.001', 1),
(7, 'E89.1076.003', 1),
(8, 'E85.1201.001', 1),
(9, 'E85.0056.038', 1),
(10, 'E17.2030.001', 1),
(11, 'E47.4747.001', 1),
(12, 'E85.0036.005', 1),
(13, 'E85.2939.001', 1),
(14, 'E85.0063.019', 1),
(15, 'E85.0092.010', 1),
(16, 'E85.1034.023', 1),
(17, 'E85.1078.001', 1),
(18, 'E85.1080.018', 1),
(19, 'E17.1174.001', 1),
(20, 'E17.2953.001', 1),
(41, 'E85.2021.001', 2);

-- --------------------------------------------------------

--
-- Table structure for table `courses_users`
--

DROP TABLE IF EXISTS `courses_users`;
CREATE TABLE IF NOT EXISTS `courses_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `course_id` varchar(12) NOT NULL,
  `user_id` varchar(9) NOT NULL,
  `credits` tinyint(4) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_user` (`course_id`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=148 ;

--
-- Dumping data for table `courses_users`
--

INSERT INTO `courses_users` (`id`, `course_id`, `user_id`, `credits`) VALUES
(1, 'E85.2021.001', 'N11111111', NULL),
(2, 'E85.2042.001', 'N22222222', NULL),
(3, 'E85.2501.001', 'N33333333', NULL),
(4, 'E85.2512.001', 'N44444444', NULL),
(5, 'E85.2517.001', 'N55555555', NULL),
(6, 'E03.0002.001', 'N66666666', NULL),
(7, 'E89.1076.003', 'N77777777', NULL),
(8, 'E85.1201.001', 'N88888888', NULL),
(9, 'E85.0056.038', 'N99999999', NULL),
(10, 'E17.2030.001', 'N10101010', NULL),
(11, 'E47.4747.001', 'N11111111', NULL),
(12, 'E85.0036.005', 'N12121212', NULL),
(13, 'E85.2939.001', 'N13131313', NULL),
(14, 'E85.0063.019', 'N14141414', NULL),
(15, 'E85.0092.010', 'N15151515', NULL),
(16, 'E85.1034.023', 'N16161616', NULL),
(17, 'E85.1078.001', 'N17171717', NULL),
(18, 'E85.1080.018', 'N18181818', NULL),
(19, 'E85.1080.018', 'N19191919', NULL),
(20, 'E17.1174.001', 'N20202020', NULL),
(21, 'E17.2953.001', 'N21212121', NULL),
(102, 'E85.2021.001', 'N14006241', 2),
(104, 'E03.0002.001', 'N14006243', 2),
(105, 'E89.1076.003', 'N14006244', 2),
(106, 'E85.1201.001', 'N14006245', 2),
(107, 'E85.0056.038', 'N14006246', 2),
(108, 'E17.2030.001', 'N14006247', 2),
(146, 'E85.2042.001', 'N14006242', 2),
(110, 'E85.0036.005', 'N14006249', 2),
(111, 'E85.0063.019', 'N14006250', 2),
(112, 'E85.0092.010', 'N14006251', 2),
(113, 'E17.1174.001', 'N14006252', 2),
(114, 'E85.0034.068', 'N14006253', 2),
(115, 'E85.0056.038', 'N14006254', 2),
(116, 'E85.0063.022', 'N14006255', 2),
(117, 'E79.0607.006', 'N14006256', 2),
(118, 'E85.2505.001', 'N14006257', 2),
(119, 'E03.0003.001', 'N14006258', 2),
(120, 'E85.1047.001', 'N14006259', 2),
(121, 'E85.0092.001', 'N14006260', 2),
(132, 'E85.2042.001', 'N16176849', 4),
(131, 'E85.1201.001', 'N14006242', 2),
(130, 'E85.2021.001', 'N14006242', 2),
(144, 'E85.2042.001', 'N11111111', NULL),
(135, 'E85.0036.005', 'N16176849', 4),
(147, 'E85.1201.001', 'N16176849', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

DROP TABLE IF EXISTS `lessons`;
CREATE TABLE IF NOT EXISTS `lessons` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `time_slot_id` int(11) unsigned NOT NULL,
  `student_id` varchar(9) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `timeslot_student` (`time_slot_id`,`student_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `time_slot_id`, `student_id`, `start_time`, `end_time`) VALUES
(48, 16, 'N14006242', '17:00:00', '17:30:00'),
(34, 17, 'N16176849', '14:00:00', '15:00:00'),
(9, 17, 'N14006250', '15:30:00', '16:00:00'),
(10, 17, 'N14006251', '18:05:00', '18:35:00'),
(15, 17, 'N14006242', '17:10:00', '17:40:00'),
(53, 27, 'N16176849', '09:30:00', '10:30:00'),
(35, 24, 'N16176849', '18:45:00', '19:15:00'),
(64, 18, 'N14006242', '18:50:00', '19:20:00'),
(38, 18, 'N16176849', '19:35:00', '20:35:00'),
(39, 16, 'N14006241', '17:45:00', '18:15:00'),
(61, 25, 'N14006247', '16:55:00', '17:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE IF NOT EXISTS `periods` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `type` varchar(7) NOT NULL,
  `year` smallint(4) NOT NULL,
  `starts` date NOT NULL,
  `ends` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='type could be enum but cakephp doesnt support it.\nxx: year ?' AUTO_INCREMENT=3 ;

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
CREATE TABLE IF NOT EXISTS `periods_students` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `period_id` int(11) unsigned NOT NULL,
  `student_id` varchar(9) NOT NULL,
  `1st_login` tinyint(1) default '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `period_user` (`period_id`,`student_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=62 ;

--
-- Dumping data for table `periods_students`
--

INSERT INTO `periods_students` (`id`, `period_id`, `student_id`, `1st_login`) VALUES
(1, 2, 'N16176849', 0),
(21, 1, 'N16176849', 0),
(61, 1, 'N14006260', 1),
(60, 1, 'N14006259', 1),
(59, 1, 'N14006258', 1),
(58, 1, 'N14006257', 1),
(57, 1, 'N14006256', 1),
(56, 1, 'N14006255', 0),
(55, 1, 'N14006254', 1),
(54, 1, 'N14006253', 1),
(53, 1, 'N14006252', 1),
(52, 1, 'N14006251', 1),
(51, 1, 'N14006250', 1),
(50, 1, 'N14006249', 1),
(49, 1, 'N14006248', 1),
(48, 1, 'N14006247', 1),
(47, 1, 'N14006246', 1),
(46, 1, 'N14006245', 1),
(45, 1, 'N14006244', 1),
(44, 1, 'N14006243', 1),
(43, 1, 'N14006242', 1),
(42, 1, 'N14006241', 1);

-- --------------------------------------------------------

--
-- Table structure for table `space_requests`
--

DROP TABLE IF EXISTS `space_requests`;
CREATE TABLE IF NOT EXISTS `space_requests` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `student_id` varchar(9) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `space_requests`
--


-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

DROP TABLE IF EXISTS `time_slots`;
CREATE TABLE IF NOT EXISTS `time_slots` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `faculty_id` varchar(9) NOT NULL,
  `course_id` varchar(12) NOT NULL,
  `day` char(1) default NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `time_avail` (`faculty_id`,`day`,`start_time`,`end_time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=45 ;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `faculty_id`, `course_id`, `day`, `start_time`, `end_time`, `location`) VALUES
(16, 'N11111111', 'E85.2021.001', 'T', '16:51:00', '18:47:00', 'EDUC 307'),
(17, 'N11111111', 'E85.2042.001', 'W', '09:55:00', '18:36:00', 'EDUC 307'),
(18, 'N22222222', 'E85.2042.001', 'T', '18:45:00', '20:35:00', 'SILV 808'),
(19, 'N33333333', 'E85.2501.001', 'W', '19:15:00', '09:20:00', 'SILV 411'),
(20, 'N44444444', 'E85.2512.001', 'M', '19:15:00', '09:20:00', 'SILV 401'),
(21, 'N11111111', 'E85.2517.001', 'F', '18:00:00', '19:00:00', 'SILV 405'),
(22, 'N66666666', 'E03.0002.001', 'F', '14:00:00', '15:55:00', '194M 203'),
(23, 'N77777777', 'E89.1076.003', 'T', '18:45:00', '19:48:00', 'EDUC 304'),
(24, 'N11111111', 'E85.1201.001', 'R', '18:45:00', '19:25:00', 'EDUC 770'),
(25, 'N10101010', 'E17.2030.001', 'M', '16:55:00', '18:35:00', 'SILV 701'),
(26, 'N12121212', 'E85.0036.005', 'M', '09:30:00', '10:05:00', 'EDUC 879'),
(27, 'N12121212', 'E85.0036.005', 'W', '09:30:00', '11:45:00', 'EDUC 879'),
(28, 'N15151515', 'E85.0092.010', 'W', '14:00:00', '15:15:00', '383L 402'),
(29, 'N17171717', 'E85.1078.001', 'W', '16:55:00', '18:35:00', 'EDUC 176'),
(30, 'N18181818', 'E85.1080.018', 'W', '07:00:00', '10:00:00', 'EDUC 13FL'),
(31, 'N20202020', 'E17.1174.001', 'W', '16:55:00', '18:35:00', 'GODD C'),
(32, 'N21212121', 'E17.2953.001', 'T', '18:45:00', '08:25:00', 'EDUC 306'),
(33, 'N99999999', 'E85.2021.001', 'M', '09:00:00', '21:00:00', 'EDUC 777'),
(37, 'N11111111', 'E85.2021.001', 'T', '16:55:00', '18:35:00', 'EDUC 307'),
(38, 'N11111111', 'E85.2021.001', 'R', '16:55:00', '18:35:00', 'EDUC 307'),
(39, 'N55555555', 'E85.2517.001', 'F', '18:00:00', '08:00:00', 'SILV 405'),
(40, 'N77777777', 'E89.1076.003', 'T', '18:45:00', '08:50:00', 'EDUC 304'),
(41, 'N88888888', 'E85.1201.001', 'R', '18:45:00', '08:25:00', 'EDUC 770'),
(42, 'N12121212', 'E85.0036.005', 'M', '09:30:00', '10:45:00', 'EDUC 879'),
(43, 'N12121212', 'E85.0036.005', 'W', '09:30:00', '10:45:00', 'EDUC 879'),
(44, 'N11111111', 'E85.2021.001', 'F', '09:00:00', '15:00:00', 'EDUC 307');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
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
('N10101010', '140d3ea2b0c7a720b8fcc236deedd04f', 'faculty', ' DESIREE P', 'HAMBURGER', 'dph999@nyu.edu', '', NULL),
('N99999999', NULL, 'faculty', ' EUNMI', 'GO', NULL, NULL, NULL),
('N88888888', NULL, 'faculty', ' AMY H', 'GOLDIN', NULL, NULL, NULL),
('N77777777', NULL, 'faculty', ' MIRI LYSSA', 'PARK', NULL, NULL, NULL),
('N66666666', NULL, 'faculty', ' PATRICIA M', 'CAREY', NULL, NULL, NULL),
('N55555555', NULL, 'faculty', ' CATHERINE J', 'MOORE', NULL, NULL, NULL),
('N44444444', NULL, 'faculty', ' JUDY H', 'TINT', NULL, NULL, NULL),
('N33333333', NULL, 'faculty', ' CHARLES J', 'SANDERS', NULL, NULL, NULL),
('N22222222', 'ed3308cb0d8840f8a2a9e3e19b172c85', 'faculty', ' ROBERT', 'ROWE', 'rrr@nyu.edu', 'comming soon...', NULL),
('N16176849', 'cd73502828457d15655bbd7a63fb0bc8', 'student', 'Jorge', 'Orpinel', 'jao327@nyu.edu', '(347) 569 0279', 'n'), -- pword: student
('admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Jorge', 'Orpinel', 'jorge@orpinel.com', NULL, NULL), -- pword: admin
('N11111111', NULL, 'faculty', ' MARISSA D', 'SILVERMAN', NULL, NULL, NULL),
('N12121212', NULL, 'faculty', ' ALFRED', 'BISHAI', NULL, NULL, NULL),
('N13131313', NULL, 'faculty', ' JOHN', 'GILBERT', NULL, NULL, NULL),
('N14141414', NULL, 'faculty', ' BRONSON N', 'MURPHY', NULL, NULL, NULL),
('N15151515', NULL, 'faculty', ' SEAN S', 'REED', NULL, NULL, NULL),
('N16161616', NULL, 'faculty', ' JONATHAN L', 'HAAS', NULL, NULL, NULL),
('N17171717', NULL, 'faculty', ' ANTON J', 'VISHIO', NULL, NULL, NULL),
('N18181818', NULL, 'faculty', ' JONATHAN L', 'HAAS', NULL, NULL, NULL),
('N19191919', NULL, 'faculty', ' JOSHUA R', 'QUILLEN', NULL, NULL, NULL),
('N20202020', NULL, 'faculty', ' LOTTIE E.', 'PORCH', NULL, NULL, NULL),
('N21212121', NULL, 'faculty', ' EDITH L', 'DEMAS', NULL, NULL, NULL),
('N14006255', '8fa3529c028babed4a83d7c67311909d', 'student', ' Mahalet M', 'Dejene', 'mmd255@nyu.edu', '55955571', 'n'),
('N14006254', NULL, 'student', ' Jessica Y.', 'Yoon', 'jyy254@nyu.edu', NULL, 'n'),
('N14006253', NULL, 'student', ' Sarah', 'Min', NULL, NULL, 'n'),
('N14006252', NULL, 'student', ' Lauren', 'Lydiard', NULL, NULL, 'm'),
('N14006251', NULL, 'student', ' Francesco A.', 'Tyl-Berwick', NULL, NULL, 'n'),
('N14006250', NULL, 'student', ' Richard', 'Yu', NULL, 'no phone', 'm'),
('N14006249', NULL, 'student', ' Eun Sun', 'Shim', 'ess249@nyu.edu', 'none', 'm'),
('N14006248', NULL, 'student', ' Andrew B.', 'Barkan', NULL, NULL, 'm'),
('N14006247', NULL, 'student', ' Adi', 'Ortner', NULL, NULL, 'n'),
('N14006246', NULL, 'student', ' Waverly E.', 'Herbert', NULL, NULL, 'n'),
('N14006245', NULL, 'student', ' Erika A.', 'Dorbad', NULL, NULL, 'n'),
('N14006244', NULL, 'student', ' Ann Margaret', 'Santa-Ines', NULL, NULL, 'n'),
('N14006243', NULL, 'student', ' Lilly A', 'Nhan', NULL, NULL, 'n'),
('N14006242', NULL, 'student', ' Scott F.', 'Berenson', 'sfb242@nyu.edu', NULL, 'm'),
('N14006241', NULL, 'student', ' Paul J.', 'Livanos', NULL, NULL, 'm'),
('N14006256', NULL, 'student', ' Javy X.', 'Rodriguez', NULL, NULL, 'm'),
('N14006257', NULL, 'student', ' Megan Murray', 'Atchley', NULL, NULL, 'n'),
('N14006258', NULL, 'student', ' Amanda J.', 'Bicking', NULL, NULL, 'm'),
('N14006259', NULL, 'student', ' Brandon H.', 'Ives', 'bhi259@nyu.edu', NULL, 'm'),
('N14006260', NULL, 'student', ' Matthew S.', 'Hayon', NULL, NULL, 'n'),
('Bill', NULL, 'admin', 'William', 'Right', 'bill@nyu.edu', '', NULL),
('TimB', NULL, 'admin', 'Tim', 'B', 'tim@nyu.edu', '', NULL),
('Ingrid', '1188dac5a0098a4558a9a5753bdd7a2e', 'admin', 'Ingrid', 'Green', 'ig17@nyu.edu', '', NULL),
('N9919919', NULL, 'faculty', 'Someone', 'ThisGuy', 'sss@nyu.edu', '', NULL);
