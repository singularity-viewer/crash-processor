-- MySQL dump 10.13  Distrib 5.5.37, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: singucrash
-- ------------------------------------------------------
-- Server version	5.5.37-0ubuntu0.14.04.1


--
-- Table structure for table `builds`
--

DROP TABLE IF EXISTS `builds`;
CREATE TABLE `builds` (
  `build_nr` int(11) NOT NULL DEFAULT '0',
  `chan` varchar(64) NOT NULL DEFAULT '',
  `version` varchar(64) DEFAULT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`chan`,`build_nr`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `signature_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `commented` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `options`
--

DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `name` varchar(128) DEFAULT NULL,
  `value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `raw_reports`
--

DROP TABLE IF EXISTS `raw_reports`;
CREATE TABLE `raw_reports` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `reported` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed` int(11) NOT NULL DEFAULT '0',
  `raw_data` longtext,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reported` timestamp NULL DEFAULT NULL,
  `client_version` varchar(32) DEFAULT NULL,
  `client_channel` varchar(32) DEFAULT NULL,
  `os` varchar(128) DEFAULT NULL,
  `os_type` varchar(32) DEFAULT NULL,
  `os_version` varchar(128) DEFAULT NULL,
  `cpu` varchar(128) DEFAULT NULL,
  `gpu` varchar(128) DEFAULT NULL,
  `opengl_version` varchar(128) DEFAULT NULL,
  `gpu_driver` varchar(128) DEFAULT NULL,
  `ram` int(11) DEFAULT NULL,
  `grid` varchar(128) DEFAULT NULL,
  `region` varchar(128) DEFAULT NULL,
  `crash_reason` varchar(128) DEFAULT NULL,
  `crash_address` varchar(16) DEFAULT NULL,
  `crash_thread` int(11) DEFAULT NULL,
  `raw_stacktrace` longtext,
  `signature_id` int(11) DEFAULT NULL,
  `client_version_s` varchar(32) DEFAULT NULL,
  `client_arch` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ix_reports_region_grid` (`region`,`grid`),
  KEY `ix_reports_signature` (`signature_id`),
  KEY `ix_ver` (`client_version_s`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `sid` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `authenticated` tinyint(4) DEFAULT NULL,
  `expires` datetime DEFAULT NULL,
  `persist` text,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `signature`
--

DROP TABLE IF EXISTS `signature`;
CREATE TABLE `signature` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL,
  `signature` text,
  `has_comments` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT NULL,
  `is_allowed` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Dump completed on 2014-05-20  9:06:08
