-- phpMyAdmin SQL Dump
-- version 4.0.0
-- http://www.phpmyadmin.net
--
-- Hoszt: localhost
-- Létrehozás ideje: 2018. Már 08. 21:18
-- Szerver verzió: 5.6.38
-- PHP verzió: 5.6.30-0+deb8u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Adatbázis: `taskmanager`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_customer`
--

CREATE TABLE IF NOT EXISTS `stm_customer` (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `color` char(7) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_issue`
--

CREATE TABLE IF NOT EXISTS `stm_issue` (
  `issue_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `sprint_id` int(11) DEFAULT NULL,
  `opener_user_id` int(11) DEFAULT NULL,
  `responsive_user_id` int(11) DEFAULT NULL,
  `status` enum('initial','confirmed','canceled','closed') NOT NULL,
  PRIMARY KEY (`issue_id`),
  KEY `project_id` (`project_id`),
  KEY `sprint_id` (`sprint_id`),
  KEY `opener_user_id` (`opener_user_id`),
  KEY `responsive_user_id` (`responsive_user_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_project`
--

CREATE TABLE IF NOT EXISTS `stm_project` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime_created` datetime NOT NULL,
  `status` enum('initial','progress','canceled','completed') NOT NULL DEFAULT 'initial',
  `label` varchar(255) NOT NULL DEFAULT '',
  `description` text,
  `has_startdate` tinyint(1) NOT NULL DEFAULT '0',
  `date_startdate` date DEFAULT NULL,
  `has_duedate` tinyint(1) NOT NULL DEFAULT '0',
  `date_duedate` date DEFAULT NULL,
  PRIMARY KEY (`project_id`),
  KEY `date_duedate` (`date_duedate`),
  KEY `date_startdate` (`date_startdate`),
  KEY `status` (`status`),
  KEY `has_startdate` (`has_startdate`),
  KEY `has_duedate` (`has_duedate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- A tábla adatainak kiíratása `stm_project`
--

INSERT INTO `stm_project` (`project_id`, `datetime_created`, `status`, `label`, `description`, `has_startdate`, `date_startdate`, `has_duedate`, `date_duedate`) VALUES
(1, '2017-05-23 00:00:00', 'progress', 'Project 1', 'Ez egy tesztprojekt.', 1, '2018-01-01', 1, '2018-06-20'),
(2, '2017-05-23 00:00:00', 'initial', 'Project 2', '', 0, NULL, 0, NULL),
(3, '2017-05-23 00:00:00', 'progress', 'Project 3', '', 0, NULL, 1, '2018-11-21'),
(4, '2017-05-23 00:00:00', 'canceled', 'Project 4', 'Negyedik', 0, NULL, 0, NULL),
(5, '2017-05-23 00:00:00', 'initial', 'Project 5', '', 0, NULL, 0, NULL),
(9, '2018-03-02 16:55:26', 'initial', 'Új projekt', NULL, 0, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_sprint`
--

CREATE TABLE IF NOT EXISTS `stm_sprint` (
  `sprint_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `status` enum('initial','progress','canceled','completed') NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`sprint_id`),
  KEY `project_id` (`project_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- A tábla adatainak kiíratása `stm_sprint`
--

INSERT INTO `stm_sprint` (`sprint_id`, `project_id`, `status`, `label`) VALUES
(1, 1, 'initial', 'Sprint1'),
(2, 1, 'initial', 'Sprint2');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_task`
--

CREATE TABLE IF NOT EXISTS `stm_task` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `has_project` tinyint(4) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `has_sprint` tinyint(1) NOT NULL,
  `sprint_id` int(11) DEFAULT NULL,
  `has_issue` tinyint(1) NOT NULL,
  `issue_id` int(11) NOT NULL,
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
  KEY `has_project_2` (`has_project`),
  KEY `project_id_2` (`project_id`),
  KEY `has_issue` (`has_issue`),
  KEY `issue_id` (`issue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `stm_user`
--

CREATE TABLE IF NOT EXISTS `stm_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `role` enum('developer','manager','customer') NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash_password` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `company_id` (`company_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
