-- phpMyAdmin SQL Dump
-- version 4.0.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 23, 2017 at 09:37 PM
-- Server version: 5.6.38
-- PHP Version: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `taskmanager`
--

-- --------------------------------------------------------

--
-- Table structure for table `stm_company`
--

DROP TABLE IF EXISTS `stm_company`;
CREATE TABLE `stm_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `color` char(7) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stm_project`
--

DROP TABLE IF EXISTS `stm_project`;
CREATE TABLE `stm_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `status` enum('initial','progress','canceled','completed') NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `has_startdate` tinyint(1) NOT NULL,
  `date_startdate` date DEFAULT NULL,
  `has_duedate` tinyint(1) NOT NULL,
  `date_duedate` date DEFAULT NULL,
  `datetime_created` datetime NOT NULL,
  PRIMARY KEY (`project_id`),
  KEY `date_duedate` (`date_duedate`),
  KEY `date_startdate` (`date_startdate`),
  KEY `status` (`status`),
  KEY `has_startdate` (`has_startdate`),
  KEY `has_duedate` (`has_duedate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stm_sprint`
--

DROP TABLE IF EXISTS `stm_sprint`;
CREATE TABLE `stm_sprint` (
  `sprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `status` enum('initial','progress','canceled','completed') NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`sprint_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stm_task`
--

DROP TABLE IF EXISTS `stm_task`;
CREATE TABLE `stm_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `has_sprint` tinyint(1) NOT NULL,
  `sprint_id` int(11) DEFAULT NULL,
  `has_ticket` tinyint(1) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `type` enum('task','requirement','userstory') NOT NULL,
  `is_management_confirmation_required` tinyint(1) NOT NULL,
  `is_client_confirmation_required` tinyint(1) NOT NULL,
  `is_confirmed_by_management` tinyint(1) NOT NULL,
  `is_confirmed_by_client` tinyint(1) NOT NULL,
  `status` enum('created','progress','paused','developed','ready','accepted') NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `datetime_created` datetime NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `has_project` (`has_sprint`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `project_id` (`sprint_id`),
  KEY `is_management_confirmation_required` (`is_management_confirmation_required`),
  KEY `is_client_confirmation_required` (`is_client_confirmation_required`),
  KEY `is_confirmed_by_management` (`is_confirmed_by_management`),
  KEY `is_confirmed_by_client` (`is_confirmed_by_client`),
  KEY `has_ticket` (`has_ticket`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stm_ticket`
--

DROP TABLE IF EXISTS `stm_ticket`;
CREATE TABLE `stm_ticket` (
  `ticket_id` int(11) NOT NULL AUTO_INCREMENT,
  `has_project` tinyint(1) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`ticket_id`),
  KEY `has_project` (`has_project`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `stm_user`
--

DROP TABLE IF EXISTS `stm_user`;
CREATE TABLE `stm_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `role` enum('developer','manager','customer') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
