-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 27, 2008 at 04:21 PM
-- Server version: 5.0.45
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `astra`
-- 
CREATE DATABASE `astra` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `astra`;

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_access_levels`
-- 

CREATE TABLE `astra_access_levels` (
  `access_level_key` tinyint(4) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`access_level_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `astra_access_levels`
-- 

INSERT INTO `astra_access_levels` VALUES (1, 'Super Admin');
INSERT INTO `astra_access_levels` VALUES (2, 'Admin');
INSERT INTO `astra_access_levels` VALUES (3, 'User');
INSERT INTO `astra_access_levels` VALUES (4, 'Guest');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_calendar_events`
-- 

CREATE TABLE `astra_calendar_events` (
  `event_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `name` varchar(60) NOT NULL default '',
  `description` text NOT NULL,
  `event_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `remove_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `event_date_finish` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_flags` tinyint(4) unsigned NOT NULL default '0',
  `link` varchar(200) NOT NULL default '',
  `event_type` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`event_key`),
  KEY `module_keyIdx` (`module_key`),
  KEY `event_dateIdx` (`event_date`),
  KEY `event_date_finishIdx` (`event_date_finish`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `astra_calendar_events`
-- 

INSERT INTO `astra_calendar_events` VALUES (1, 25, 4, 'jhkhjk', 'hjkjhk', '2007-12-25 12:00:00', '0000-00-00 00:00:00', '2007-12-25 11:40:15', '2007-12-25 13:00:00', 0, '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_calendars`
-- 

CREATE TABLE `astra_calendars` (
  `module_key` int(11) NOT NULL default '0',
  `parent_calendar_key` int(11) NOT NULL default '0',
  `type` varchar(6) NOT NULL default '',
  UNIQUE KEY `module_key` (`module_key`,`parent_calendar_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_calendars`
-- 

INSERT INTO `astra_calendars` VALUES (25, 0, 'closed');
INSERT INTO `astra_calendars` VALUES (26, 0, 'open');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_chat`
-- 

CREATE TABLE `astra_chat` (
  `module_key` int(11) unsigned NOT NULL default '0',
  `last_clean` datetime default NULL,
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_chat`
-- 

INSERT INTO `astra_chat` VALUES (6, '2007-09-26 04:04:38');
INSERT INTO `astra_chat` VALUES (7, NULL);
INSERT INTO `astra_chat` VALUES (8, NULL);
INSERT INTO `astra_chat` VALUES (9, NULL);
INSERT INTO `astra_chat` VALUES (10, NULL);
INSERT INTO `astra_chat` VALUES (14, NULL);
INSERT INTO `astra_chat` VALUES (17, '2007-12-18 09:16:42');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_chat_events`
-- 

CREATE TABLE `astra_chat_events` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `time` datetime NOT NULL default '0000-00-00 00:00:00',
  `for_user` text,
  `data` text,
  PRIMARY KEY  (`id`),
  KEY `module_key` (`module_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- 
-- Dumping data for table `astra_chat_events`
-- 

INSERT INTO `astra_chat_events` VALUES (1, 6, '2007-09-26 04:04:38', '', '<join user_key="4" handle="shafiq issani" role="none"></join>');
INSERT INTO `astra_chat_events` VALUES (2, 17, '2007-12-18 09:16:42', '', '<join user_key="4" handle="Administrator " role="none"></join>');
INSERT INTO `astra_chat_events` VALUES (3, 17, '2007-12-18 09:16:47', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (4, 17, '2007-12-18 09:16:54', '', '<status user_key="4" handle="Administrator " activity="-1"></status>');
INSERT INTO `astra_chat_events` VALUES (5, 17, '2007-12-18 09:16:56', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (6, 17, '2007-12-18 09:16:57', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (7, 17, '2007-12-18 09:16:57', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (8, 17, '2007-12-18 09:16:59', '', '<message user_key="4" handle="Administrator " activity="-1">fdhgcvbcvbcvb</message>');
INSERT INTO `astra_chat_events` VALUES (9, 17, '2007-12-18 09:17:01', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (10, 17, '2007-12-18 09:17:01', '', '<message user_key="4" handle="Administrator " activity="-1">j%2Cfxcbv</message>');
INSERT INTO `astra_chat_events` VALUES (11, 17, '2007-12-18 09:17:01', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (12, 17, '2007-12-18 09:17:01', '', '<message user_key="4" handle="Administrator " activity="-1">dfb</message>');
INSERT INTO `astra_chat_events` VALUES (13, 17, '2007-12-18 09:17:02', '', '<status user_key="4" handle="Administrator " activity="0"></status>');
INSERT INTO `astra_chat_events` VALUES (14, 17, '2007-12-18 09:17:02', '', '<message user_key="4" handle="Administrator " activity="-1">dfb</message>');
INSERT INTO `astra_chat_events` VALUES (15, 17, '2007-12-18 09:17:02', '', '<status user_key="4" handle="Administrator " activity="0"></status>');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_chat_users`
-- 

CREATE TABLE `astra_chat_users` (
  `module_key` int(11) unsigned NOT NULL default '0',
  `user_key` int(11) default NULL,
  `handle` varchar(20) NOT NULL default '',
  `role` varchar(20) NOT NULL default 'none',
  `last_poll` datetime default NULL,
  `last_poll_id` int(11) unsigned NOT NULL default '0',
  `status` tinyint(3) unsigned NOT NULL default '0',
  KEY `module_key` (`module_key`),
  KEY `user_key` (`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_chat_users`
-- 

INSERT INTO `astra_chat_users` VALUES (6, 4, 'shafiq issani', 'none', '2007-09-26 04:04:38', 1, 0);
INSERT INTO `astra_chat_users` VALUES (17, 4, 'Administrator ', 'none', '2007-12-18 09:17:02', 15, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_colours`
-- 

CREATE TABLE `astra_colours` (
  `colour` char(6) NOT NULL default '000000'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_colours`
-- 

INSERT INTO `astra_colours` VALUES ('900000');
INSERT INTO `astra_colours` VALUES ('701818');
INSERT INTO `astra_colours` VALUES ('863200');
INSERT INTO `astra_colours` VALUES ('522500');
INSERT INTO `astra_colours` VALUES ('374310');
INSERT INTO `astra_colours` VALUES ('007000');
INSERT INTO `astra_colours` VALUES ('227070');
INSERT INTO `astra_colours` VALUES ('004080');
INSERT INTO `astra_colours` VALUES ('000090');
INSERT INTO `astra_colours` VALUES ('400080');
INSERT INTO `astra_colours` VALUES ('800080');
INSERT INTO `astra_colours` VALUES ('800040');
INSERT INTO `astra_colours` VALUES ('844444');
INSERT INTO `astra_colours` VALUES ('CE5C00');
INSERT INTO `astra_colours` VALUES ('98722F');
INSERT INTO `astra_colours` VALUES ('70703C');
INSERT INTO `astra_colours` VALUES ('497749');
INSERT INTO `astra_colours` VALUES ('4C8162');
INSERT INTO `astra_colours` VALUES ('3D6381');
INSERT INTO `astra_colours` VALUES ('434384');
INSERT INTO `astra_colours` VALUES ('685872');
INSERT INTO `astra_colours` VALUES ('606060');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_common_nav_links`
-- 

CREATE TABLE `astra_common_nav_links` (
  `link_key` int(11) NOT NULL default '0',
  `space_type_key` tinyint(4) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `url` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`link_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_common_nav_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_cron`
-- 

CREATE TABLE `astra_cron` (
  `last_run` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_cron`
-- 

INSERT INTO `astra_cron` VALUES ('2007-09-25 10:49:10');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_date_formats`
-- 

CREATE TABLE `astra_date_formats` (
  `format_key` int(11) NOT NULL default '0',
  `format` varchar(15) NOT NULL default '',
  `type` varchar(5) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_date_formats`
-- 

INSERT INTO `astra_date_formats` VALUES (0, 'dd-mm-yy', 'short');
INSERT INTO `astra_date_formats` VALUES (1, 'mm-dd-yy', 'short');
INSERT INTO `astra_date_formats` VALUES (2, 'd-m-yy', 'short');
INSERT INTO `astra_date_formats` VALUES (3, 'm-d-yy', 'short');
INSERT INTO `astra_date_formats` VALUES (4, 'dd-mm-yyyy', 'short');
INSERT INTO `astra_date_formats` VALUES (5, 'mm-dd-yyyy', 'short');
INSERT INTO `astra_date_formats` VALUES (6, 'd-m-yyyy', 'short');
INSERT INTO `astra_date_formats` VALUES (7, 'm-d-yyyy', 'short');
INSERT INTO `astra_date_formats` VALUES (0, 'd Jan 2003', 'long');
INSERT INTO `astra_date_formats` VALUES (1, 'd January 2003', 'long');
INSERT INTO `astra_date_formats` VALUES (2, 'January d 2003', 'long');
INSERT INTO `astra_date_formats` VALUES (3, 'Jan d 2003', 'long');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_default_space_user_links`
-- 

CREATE TABLE `astra_default_space_user_links` (
  `user_group_key` int(11) NOT NULL default '0',
  `space_key` int(11) NOT NULL default '0',
  `permanent` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `user_group_key` (`user_group_key`,`space_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_default_space_user_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_dropbox_download_links`
-- 

CREATE TABLE `astra_dropbox_download_links` (
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `time_downloaded` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_uploaded` datetime NOT NULL default '0000-00-00 00:00:00',
  `filename` varchar(45) NOT NULL default '',
  UNIQUE KEY `module_key` (`module_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_dropbox_download_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_dropbox_file_status`
-- 

CREATE TABLE `astra_dropbox_file_status` (
  `status_key` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`status_key`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `astra_dropbox_file_status`
-- 

INSERT INTO `astra_dropbox_file_status` VALUES (1, 'Submitted');
INSERT INTO `astra_dropbox_file_status` VALUES (2, 'Marked');
INSERT INTO `astra_dropbox_file_status` VALUES (3, 'Resubmit required');
INSERT INTO `astra_dropbox_file_status` VALUES (4, 'Being marked');
INSERT INTO `astra_dropbox_file_status` VALUES (5, 'Pass');
INSERT INTO `astra_dropbox_file_status` VALUES (6, 'Fail');
INSERT INTO `astra_dropbox_file_status` VALUES (7, 'Reviewed');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_dropbox_files`
-- 

CREATE TABLE `astra_dropbox_files` (
  `file_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `gradebook_item_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` tinyint(4) NOT NULL default '0',
  `date_status_changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `description` varchar(50) NOT NULL default '',
  `comments` text NOT NULL,
  `filename` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`file_key`),
  KEY `module_keyIdx` (`module_key`),
  KEY `user_keyIdx` (`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_dropbox_files`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_dropbox_settings`
-- 

CREATE TABLE `astra_dropbox_settings` (
  `module_key` int(11) NOT NULL default '0',
  `file_path` varchar(20) NOT NULL default '',
  `type_key` tinyint(1) NOT NULL default '1',
  `time_allowed` int(4) NOT NULL default '0',
  `download_file` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_dropbox_settings`
-- 

INSERT INTO `astra_dropbox_settings` VALUES (18, '49/18', 1, 0, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_event_types`
-- 

CREATE TABLE `astra_event_types` (
  `event_type_key` int(11) NOT NULL auto_increment,
  `parent` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `colour` varchar(6) NOT NULL default '000000',
  PRIMARY KEY  (`event_type_key`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_event_types`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_feedreader_settings`
-- 

CREATE TABLE `astra_feedreader_settings` (
  `module_key` int(11) NOT NULL default '0',
  `url` mediumtext NOT NULL,
  `file_path` varchar(20) NOT NULL default '',
  `item_count` int(2) NOT NULL default '0',
  PRIMARY KEY  (`module_key`),
  KEY `module_keyIdx` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_feedreader_settings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_files`
-- 

CREATE TABLE `astra_files` (
  `module_key` int(11) NOT NULL default '0',
  `file_path` varchar(50) NOT NULL default '',
  `filename` varchar(50) NOT NULL default '',
  `embedded` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_files`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_folder_settings`
-- 

CREATE TABLE `astra_folder_settings` (
  `module_key` int(11) NOT NULL default '0',
  `sort_order_key` tinyint(4) NOT NULL default '0',
  `navigation_mode` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_folder_settings`
-- 

INSERT INTO `astra_folder_settings` VALUES (22, 3, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_forum_settings`
-- 

CREATE TABLE `astra_forum_settings` (
  `module_key` int(11) NOT NULL default '0',
  `forum_type` varchar(10) NOT NULL default '',
  `edit_level` tinyint(4) NOT NULL default '0',
  `auto_prompting` tinyint(1) NOT NULL default '0',
  `days_to_wait` tinyint(2) NOT NULL default '0',
  `number_to_prompt` tinyint(2) NOT NULL default '0',
  `passes_allowed` tinyint(2) NOT NULL default '0',
  `response_time` tinyint(2) NOT NULL default '0',
  `minimum_replies` tinyint(2) NOT NULL default '0',
  `file_path` varchar(20) NOT NULL default '',
  UNIQUE KEY `module_key` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_forum_settings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_forum_thread_management`
-- 

CREATE TABLE `astra_forum_thread_management` (
  `post_key` int(11) NOT NULL default '0',
  `days_to_wait` tinyint(2) NOT NULL default '0',
  `number_to_prompt` tinyint(2) NOT NULL default '0',
  `passes_allowed` tinyint(2) NOT NULL default '0',
  `response_time` tinyint(2) NOT NULL default '0',
  `minimum_replies` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`post_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_forum_thread_management`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_gradebook_item_user_links`
-- 

CREATE TABLE `astra_gradebook_item_user_links` (
  `item_key` int(11) NOT NULL auto_increment,
  `user_key` int(11) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `grade_key` int(3) NOT NULL default '0',
  `comments` mediumtext NOT NULL,
  UNIQUE KEY `item_key` (`item_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_gradebook_item_user_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_gradebook_items`
-- 

CREATE TABLE `astra_gradebook_items` (
  `item_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `scale_key` int(11) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `url` varchar(150) NOT NULL default '',
  `content_module_key` int(11) NOT NULL default '0',
  `status_key` tinyint(1) NOT NULL default '0',
  `sort_order` tinyint(3) NOT NULL default '0',
  `due_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `maximum_score` tinyint(3) NOT NULL default '0',
  `weighting` tinyint(3) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`item_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_gradebook_items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_gradebook_scale_grades`
-- 

CREATE TABLE `astra_gradebook_scale_grades` (
  `grade_key` int(11) NOT NULL auto_increment,
  `scale_key` int(11) NOT NULL default '0',
  `grade` varchar(255) NOT NULL default '',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`grade_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- 
-- Dumping data for table `astra_gradebook_scale_grades`
-- 

INSERT INTO `astra_gradebook_scale_grades` VALUES (1, 2, 'A', 1, '2003-11-03 10:03:36', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (2, 2, 'A+', 1, '2003-11-03 10:03:44', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (3, 2, 'A-', 1, '2003-11-03 10:03:49', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (4, 2, 'B', 1, '2003-11-03 10:03:53', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (5, 2, 'B+', 1, '2003-11-03 10:03:58', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (6, 2, 'B-', 1, '2003-11-03 10:04:03', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (7, 2, 'C', 1, '2003-11-03 10:04:09', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (8, 2, 'C+', 1, '2003-11-03 10:04:13', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (9, 2, 'C-', 1, '2003-11-03 10:04:18', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (10, 2, 'D', 1, '2003-11-03 10:04:22', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (11, 2, 'D+', 1, '2003-11-03 10:04:27', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (12, 2, 'D-', 1, '2003-11-03 10:04:31', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (13, 2, 'E', 1, '2003-11-03 10:04:37', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (14, 2, 'E+', 1, '2003-11-03 10:04:45', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (15, 2, 'E-', 1, '2003-11-03 10:04:50', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (16, 2, 'F', 1, '2003-11-03 10:04:54', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (17, 3, 'Pass', 1, '2003-11-03 10:05:37', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (18, 3, 'Fail', 1, '2003-11-03 10:05:42', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (19, 3, 'Resubmit', 1, '2003-11-03 10:05:49', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (20, 3, 'Pass with distinction', 1, '2003-11-03 10:06:02', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (21, 3, 'Complete', 1, '2003-11-03 10:06:45', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scale_grades` VALUES (22, 3, 'Incomplete', 1, '2003-11-03 10:06:45', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_gradebook_scales`
-- 

CREATE TABLE `astra_gradebook_scales` (
  `scale_key` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `space_key` int(11) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`scale_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `astra_gradebook_scales`
-- 

INSERT INTO `astra_gradebook_scales` VALUES (1, 'Numeric', 'Standard numeric scale', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scales` VALUES (2, 'Alpha', 'Standard a-f Alpha scale', 0, 0, '2003-11-03 10:02:23', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scales` VALUES (3, 'Pass/Fail', '', 0, 0, '2003-11-03 10:05:25', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_gradebook_scales` VALUES (4, 'Complete', 'Basic complete/incomplete grading', 0, 0, '2003-11-03 10:06:33', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_group_settings`
-- 

CREATE TABLE `astra_group_settings` (
  `module_key` int(11) NOT NULL default '0',
  `sort_order_key` tinyint(4) NOT NULL default '0',
  `access_key` tinyint(4) NOT NULL default '0',
  `access_code` varchar(10) NOT NULL default '',
  `visibility_key` tinyint(4) NOT NULL default '0',
  `maximum_users` tinyint(4) NOT NULL default '0',
  `minimum_users` tinyint(4) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `finish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `group_management` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_group_settings`
-- 

INSERT INTO `astra_group_settings` VALUES (16, 2, 1, '', 1, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_group_user_links`
-- 

CREATE TABLE `astra_group_user_links` (
  `group_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `access_level_key` tinyint(4) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `group_key` (`group_key`,`user_key`),
  KEY `group_keyIdx` (`group_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_group_user_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_headings`
-- 

CREATE TABLE `astra_headings` (
  `module_key` int(11) NOT NULL default '0',
  `initial_state` tinyint(4) NOT NULL default '0',
  `level` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_headings`
-- 

INSERT INTO `astra_headings` VALUES (11, 0, 1);
INSERT INTO `astra_headings` VALUES (13, 0, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_icons`
-- 

CREATE TABLE `astra_icons` (
  `icon_key` int(11) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `small_icon` varchar(30) NOT NULL default '',
  `large_icon` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`icon_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `astra_icons`
-- 

INSERT INTO `astra_icons` VALUES (1, 'Default', ' Default system icon', '', '');
INSERT INTO `astra_icons` VALUES (2, ' None', 'No icon', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_journal_links`
-- 

CREATE TABLE `astra_journal_links` (
  `link_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `journal_user_key` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `url` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`link_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_journal_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_journal_settings`
-- 

CREATE TABLE `astra_journal_settings` (
  `module_key` int(11) NOT NULL default '0',
  `options` tinyint(4) unsigned NOT NULL default '0',
  `entries_to_show` int(2) NOT NULL default '0',
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `finish_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_journal_settings`
-- 

INSERT INTO `astra_journal_settings` VALUES (20, 36, 15, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_journal_user_links`
-- 

CREATE TABLE `astra_journal_user_links` (
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  UNIQUE KEY `module_key` (`module_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_journal_user_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_categories`
-- 

CREATE TABLE `astra_kb_categories` (
  `category_key` int(11) NOT NULL auto_increment,
  `parent_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`category_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_kb_categories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_entries`
-- 

CREATE TABLE `astra_kb_entries` (
  `entry_key` int(11) NOT NULL auto_increment,
  `template_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_key` tinyint(1) NOT NULL default '2',
  PRIMARY KEY  (`entry_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_kb_entries`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_entry_category_links`
-- 

CREATE TABLE `astra_kb_entry_category_links` (
  `entry_key` int(11) NOT NULL default '0',
  `category_key` int(11) NOT NULL default '0',
  UNIQUE KEY `entry_key` (`entry_key`,`category_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_kb_entry_category_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_entry_data`
-- 

CREATE TABLE `astra_kb_entry_data` (
  `data_key` int(11) NOT NULL auto_increment,
  `entry_key` int(11) NOT NULL default '0',
  `field_key` int(11) NOT NULL default '0',
  `data` text NOT NULL,
  PRIMARY KEY  (`data_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_kb_entry_data`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_fields`
-- 

CREATE TABLE `astra_kb_fields` (
  `field_key` int(11) NOT NULL auto_increment,
  `template_key` int(11) NOT NULL default '0',
  `type_key` tinyint(1) NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `display_order` tinyint(2) NOT NULL default '0',
  `number_of_lines` tinyint(2) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`field_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `astra_kb_fields`
-- 

INSERT INTO `astra_kb_fields` VALUES (1, 1, 1, 'Word', 'Enter the word to be defined', 1, 1, 4, '2004-06-08 14:08:27', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (2, 1, 1, 'Definition', 'Enter the definition of the word', 2, 15, 4, '2004-06-08 14:08:45', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (3, 2, 1, 'Question', 'Enter question including ?', 1, 1, 4, '2004-07-05 15:45:56', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (4, 2, 1, 'Answer', 'Enter the answer to the question', 2, 15, 4, '2004-07-05 15:46:17', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (5, 3, 1, 'Title', 'Title of file', 1, 1, 4, '2004-07-05 15:47:55', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (6, 3, 1, 'description', 'description of file', 2, 15, 4, '2004-07-05 15:48:13', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (7, 3, 3, 'File', 'Upload file', 3, 0, 4, '2004-07-05 15:48:30', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (8, 4, 1, 'Title', 'Title of website', 1, 1, 4, '2004-07-05 15:49:22', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (9, 4, 1, 'description', 'description of website', 2, 15, 4, '2004-07-05 15:49:42', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_fields` VALUES (10, 4, 2, 'url', 'url of website', 3, 0, 4, '2004-07-05 15:49:55', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_module_template_links`
-- 

CREATE TABLE `astra_kb_module_template_links` (
  `module_key` int(11) NOT NULL default '0',
  `template_key` int(11) NOT NULL default '0',
  UNIQUE KEY `module_key` (`module_key`,`template_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_kb_module_template_links`
-- 

INSERT INTO `astra_kb_module_template_links` VALUES (27, 1);
INSERT INTO `astra_kb_module_template_links` VALUES (27, 2);
INSERT INTO `astra_kb_module_template_links` VALUES (27, 3);
INSERT INTO `astra_kb_module_template_links` VALUES (27, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_settings`
-- 

CREATE TABLE `astra_kb_settings` (
  `module_key` int(11) NOT NULL default '0',
  `access_level_key` tinyint(4) NOT NULL default '0',
  `file_path` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_kb_settings`
-- 

INSERT INTO `astra_kb_settings` VALUES (27, 2, '65/27');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_kb_templates`
-- 

CREATE TABLE `astra_kb_templates` (
  `template_key` int(11) NOT NULL auto_increment,
  `type_key` tinyint(1) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `summary_fields` tinyint(1) NOT NULL default '1',
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`template_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `astra_kb_templates`
-- 

INSERT INTO `astra_kb_templates` VALUES (1, 0, 'Glossary', 'For entering word definitions', 1, 4, '2004-06-08 14:08:06', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_templates` VALUES (2, 0, 'FAQs', 'For entering Frequently Asked Questions', 1, 4, '2004-07-05 15:39:16', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_templates` VALUES (3, 0, 'Files', 'For sharing files', 1, 4, '2004-07-05 15:47:38', 0, '0000-00-00 00:00:00');
INSERT INTO `astra_kb_templates` VALUES (4, 0, 'links', 'For sharing links to websites', 1, 4, '2004-07-05 15:48:50', 0, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_module_edit_right_links`
-- 

CREATE TABLE `astra_module_edit_right_links` (
  `user_key` int(11) NOT NULL default '0',
  `group_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `link_key` int(11) NOT NULL default '0',
  `edit_level` tinyint(4) NOT NULL default '0',
  UNIQUE KEY `user_key` (`user_key`,`group_key`,`module_key`,`link_key`,`edit_level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_module_edit_right_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_module_space_links`
-- 

CREATE TABLE `astra_module_space_links` (
  `link_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `space_key` int(11) NOT NULL default '0',
  `parent_key` int(11) NOT NULL default '0',
  `block_key` tinyint(2) NOT NULL default '0',
  `group_key` int(11) NOT NULL default '0',
  `status_key` tinyint(4) NOT NULL default '0',
  `access_level_key` int(11) NOT NULL default '0',
  `icon_key` int(11) NOT NULL default '1',
  `added_by_key` int(11) NOT NULL default '0',
  `owner_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `edit_rights_key` tinyint(2) NOT NULL default '0',
  `change_status_date` date NOT NULL default '0000-00-00',
  `change_status_to_key` int(11) NOT NULL default '0',
  `sort_order` mediumint(4) NOT NULL default '0',
  `target` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`link_key`),
  KEY `module_keyIdx` (`module_key`),
  KEY `space_keyIdx` (`space_key`),
  KEY `parent_keyIdx` (`parent_key`),
  KEY `group_keyIdx` (`group_key`),
  KEY `date_addedIdx` (`date_added`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- 
-- Dumping data for table `astra_module_space_links`
-- 

INSERT INTO `astra_module_space_links` VALUES (1, 1, 1, 0, 0, 0, 1, 1, 1, 2, 2, '2007-09-25 10:57:45', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (2, 2, 1, 0, 0, 0, 1, 1, 1, 2, 2, '2007-09-25 10:59:37', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (3, 3, 1, 0, 0, 0, 1, 1, 1, 2, 2, '2007-09-25 11:02:17', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (4, 4, 1, 0, 0, 0, 1, 1, 1, 2, 2, '2007-09-25 11:03:15', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (5, 5, 1, 0, 0, 0, 1, 1, 1, 2, 2, '2007-09-25 11:04:19', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (6, 6, 2, 0, 1, 0, 1, 2, 1, 2, 2, '2007-09-25 11:08:00', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (7, 7, 3, 0, 1, 0, 1, 2, 1, 2, 2, '2007-09-25 11:10:09', 0, '0000-00-00 00:00:00', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (8, 8, 4, 0, 1, 0, 1, 2, 1, 2, 2, '2007-09-25 11:14:32', 2, '2007-09-25 11:20:20', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (9, 9, 5, 0, 1, 0, 1, 2, 1, 2, 2, '2007-09-25 11:17:08', 2, '2007-09-25 11:20:26', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (10, 10, 6, 0, 1, 0, 1, 2, 1, 2, 2, '2007-09-25 11:19:09', 2, '2007-09-25 11:20:30', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (11, 11, 1, 0, 0, 0, 4, 0, 1, 4, 4, '2007-09-25 13:04:46', 4, '2007-10-09 11:22:23', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (12, 12, 1, 0, 0, 0, 2, 3, 2, 4, 4, '2007-09-25 13:05:58', 4, '2007-10-09 11:24:15', 1, '0000-00-00', 2, 0, '');
INSERT INTO `astra_module_space_links` VALUES (13, 13, 1, 0, 0, 0, 4, 0, 1, 4, 4, '2007-09-26 08:35:41', 4, '2007-10-09 11:22:32', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (14, 14, 1, 0, 0, 0, 4, 3, 1, 4, 4, '2007-09-26 08:36:26', 4, '2007-10-09 11:22:08', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (15, 15, 1, 0, 0, 0, 1, 3, 1, 4, 4, '2007-10-09 11:43:23', 4, '2007-11-08 07:21:08', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (16, 16, 1, 0, 0, 16, 4, 0, 1, 4, 4, '2007-11-08 07:10:46', 4, '2007-11-08 07:12:28', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (17, 17, 1, 0, 0, 0, 4, 0, 1, 4, 4, '2007-12-18 09:16:36', 4, '2007-12-24 12:31:01', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (18, 18, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:27:53', 4, '2007-12-25 11:28:14', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (19, 19, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:28:44', 4, '2007-12-25 11:29:48', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (20, 20, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:30:40', 4, '2007-12-25 11:31:25', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (21, 21, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:33:08', 4, '2007-12-25 11:34:32', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (22, 22, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:37:49', 4, '2007-12-25 11:38:15', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (23, 23, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:38:46', 4, '2007-12-25 11:39:04', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (24, 24, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:39:29', 4, '2007-12-25 11:39:52', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (25, 25, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:40:03', 4, '2007-12-25 11:41:48', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (26, 26, 1, 0, 0, 0, 1, 3, 1, 4, 4, '2007-12-25 11:42:46', 4, '2007-12-25 11:44:36', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (27, 27, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:44:51', 4, '2007-12-25 11:45:50', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (28, 28, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2007-12-25 11:46:07', 4, '2007-12-25 11:46:32', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (29, 29, 1, 0, 1, 0, 4, 0, 1, 4, 4, '2008-01-27 09:55:47', 4, '2008-01-27 09:56:03', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (30, 30, 1, 0, 0, 0, 4, 0, 1, 4, 4, '2008-01-27 10:00:16', 4, '2008-01-27 10:00:42', 1, '0000-00-00', 0, 0, '');
INSERT INTO `astra_module_space_links` VALUES (31, 31, 1, 0, 0, 0, 4, 0, 1, 4, 4, '2008-01-27 10:01:03', 4, '2008-01-27 10:05:55', 1, '0000-00-00', 0, 0, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_module_subscription_links`
-- 

CREATE TABLE `astra_module_subscription_links` (
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `type_key` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `module_key` (`module_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_module_subscription_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_module_types`
-- 

CREATE TABLE `astra_module_types` (
  `code` varchar(15) NOT NULL default '',
  `active` tinyint(1) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_module_types`
-- 

INSERT INTO `astra_module_types` VALUES ('file', 1);
INSERT INTO `astra_module_types` VALUES ('link', 1);
INSERT INTO `astra_module_types` VALUES ('forum', 1);
INSERT INTO `astra_module_types` VALUES ('folder', 1);
INSERT INTO `astra_module_types` VALUES ('note', 1);
INSERT INTO `astra_module_types` VALUES ('group', 1);
INSERT INTO `astra_module_types` VALUES ('calendar', 1);
INSERT INTO `astra_module_types` VALUES ('dropbox', 1);
INSERT INTO `astra_module_types` VALUES ('sharing', 1);
INSERT INTO `astra_module_types` VALUES ('heading', 1);
INSERT INTO `astra_module_types` VALUES ('page', 1);
INSERT INTO `astra_module_types` VALUES ('chat', 1);
INSERT INTO `astra_module_types` VALUES ('journal', 1);
INSERT INTO `astra_module_types` VALUES ('noticeboard', 1);
INSERT INTO `astra_module_types` VALUES ('gradebook', 1);
INSERT INTO `astra_module_types` VALUES ('quiz', 1);
INSERT INTO `astra_module_types` VALUES ('kb', 1);
INSERT INTO `astra_module_types` VALUES ('space', 1);
INSERT INTO `astra_module_types` VALUES ('scorm', 1);
INSERT INTO `astra_module_types` VALUES ('feedreader', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_modules`
-- 

CREATE TABLE `astra_modules` (
  `module_key` int(11) NOT NULL auto_increment,
  `type_code` varchar(20) NOT NULL default '',
  `status_key` tinyint(4) NOT NULL default '1',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `added_by_key` int(11) NOT NULL default '0',
  `owner_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `edit_rights_key` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`module_key`),
  FULLTEXT KEY `name` (`name`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

-- 
-- Dumping data for table `astra_modules`
-- 

INSERT INTO `astra_modules` VALUES (15, 'space', 1, 'College Website News', 'Entries of changes made to the college website.', 4, 4, '2007-10-09 11:43:23', 4, '2007-11-08 07:21:08', 6);
INSERT INTO `astra_modules` VALUES (16, 'group', 4, 'Website News', 'Updates made to the college website.', 4, 4, '2007-11-08 07:10:46', 4, '2007-11-08 07:12:28', 6);
INSERT INTO `astra_modules` VALUES (17, 'chat', 4, 'chatsdfsf', 'fghgfhh', 4, 4, '2007-12-18 09:16:36', 4, '2007-12-24 12:31:01', 6);
INSERT INTO `astra_modules` VALUES (18, 'dropbox', 4, 'ghj', 'ghj', 4, 4, '2007-12-25 11:27:53', 4, '2007-12-25 11:28:14', 6);
INSERT INTO `astra_modules` VALUES (19, 'sharing', 4, 'bnm', 'gjh', 4, 4, '2007-12-25 11:28:44', 4, '2007-12-25 11:29:48', 6);
INSERT INTO `astra_modules` VALUES (20, 'journal', 4, 'hb', 'ghvj', 4, 4, '2007-12-25 11:30:40', 4, '2007-12-25 11:31:25', 6);
INSERT INTO `astra_modules` VALUES (21, 'quiz', 4, 'bnm', 'bjbhj', 4, 4, '2007-12-25 11:33:08', 4, '2007-12-25 11:34:32', 6);
INSERT INTO `astra_modules` VALUES (22, 'folder', 4, 'fghft', 'fthfth', 4, 4, '2007-12-25 11:37:49', 4, '2007-12-25 11:38:15', 6);
INSERT INTO `astra_modules` VALUES (23, 'note', 4, 'hjhgj', '', 4, 4, '2007-12-25 11:38:46', 4, '2007-12-25 11:39:04', 6);
INSERT INTO `astra_modules` VALUES (24, 'page', 4, 'hjghj', 'ghjgjh', 4, 4, '2007-12-25 11:39:29', 4, '2007-12-25 11:39:52', 6);
INSERT INTO `astra_modules` VALUES (25, 'calendar', 4, 'hbm', 'bhm', 4, 4, '2007-12-25 11:40:03', 4, '2007-12-25 11:41:48', 6);
INSERT INTO `astra_modules` VALUES (26, 'calendar', 1, 'Calendar', 'College Calendar', 4, 4, '2007-12-25 11:42:46', 4, '2007-12-25 11:44:36', 6);
INSERT INTO `astra_modules` VALUES (27, 'kb', 4, 'gfhfh', 'fghfgh', 4, 4, '2007-12-25 11:44:51', 4, '2007-12-25 11:45:50', 6);
INSERT INTO `astra_modules` VALUES (28, 'noticeboard', 4, 'fghdf', 'hdfgh', 4, 4, '2007-12-25 11:46:07', 4, '2007-12-25 11:46:32', 6);
INSERT INTO `astra_modules` VALUES (29, 'quiz', 4, 'cgfd', 'dfgdfg', 4, 4, '2008-01-27 09:55:47', 4, '2008-01-27 09:56:03', 6);
INSERT INTO `astra_modules` VALUES (30, 'space', 4, 'ghj', 'ghj', 4, 4, '2008-01-27 10:00:16', 4, '2008-01-27 10:00:42', 6);
INSERT INTO `astra_modules` VALUES (31, 'space', 4, 'gj', 'fjg', 4, 4, '2008-01-27 10:01:03', 4, '2008-01-27 10:05:55', 6);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_news`
-- 

CREATE TABLE `astra_news` (
  `news_key` int(11) NOT NULL auto_increment,
  `space_key` int(11) NOT NULL default '0',
  `heading` varchar(60) NOT NULL default '',
  `body` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `remove_date` date NOT NULL default '0000-00-00',
  `user_key` int(11) NOT NULL default '0',
  `options` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`news_key`),
  KEY `space_keyIdx` (`space_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `astra_news`
-- 

INSERT INTO `astra_news` VALUES (1, 7, 'Udbhav 2007', 'Coming up soon!<br>', '2007-09-25 13:50:40', '0000-00-00', 4, 1);
INSERT INTO `astra_news` VALUES (2, 7, 'College Fete - Udbhav 2007', 'Udbhav website launched&nbsp; today. You may access it at http://www.astra-udbhav.com<br><img src="/astra/interact/images/smileys/regular_smile.gif" height="19" width="19"><br><br><br>', '2007-09-26 06:33:32', '0000-00-00', 2, 1);
INSERT INTO `astra_news` VALUES (3, 1, 'Suggestions', 'If you''ve got any suggestions or good ideas for this website please contact :<br><br>Shafiq Issani ( 9949887398 )<br>Subhash ( 9949503076 )<br>\r\nSaiKrishna ( 9866350483)<br>', '2007-09-26 10:11:45', '0000-00-00', 4, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_notes`
-- 

CREATE TABLE `astra_notes` (
  `module_key` int(11) NOT NULL default '0',
  `note` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_notes`
-- 

INSERT INTO `astra_notes` VALUES (23, 'ghjghjhgj<img src="/astra/interact/images/smileys/sad_smile.gif" height="19" width="19">');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_noticeboard_settings`
-- 

CREATE TABLE `astra_noticeboard_settings` (
  `module_key` int(11) NOT NULL default '0',
  `type_key` tinyint(4) NOT NULL default '0',
  `days_to_keep` tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_noticeboard_settings`
-- 

INSERT INTO `astra_noticeboard_settings` VALUES (28, 2, 2);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_notices`
-- 

CREATE TABLE `astra_notices` (
  `notice_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `heading` varchar(60) NOT NULL default '',
  `body` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `remove_date` date NOT NULL default '0000-00-00',
  `user_key` int(11) NOT NULL default '0',
  PRIMARY KEY  (`notice_key`),
  KEY `space_keyIdx` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_notices`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_online_users`
-- 

CREATE TABLE `astra_online_users` (
  `user_key` int(11) NOT NULL default '0',
  `session_id` varchar(32) NOT NULL default '',
  `time` int(11) NOT NULL default '0',
  `polling` tinyint(1) NOT NULL default '0',
  `status_key` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `user_key` (`user_key`,`session_id`),
  KEY `user_key_idx` (`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_online_users`
-- 

INSERT INTO `astra_online_users` VALUES (0, '65b4d1113e85b115d4792ef9ff19fc72', 1201450875, 0, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_page_settings`
-- 

CREATE TABLE `astra_page_settings` (
  `module_key` int(11) NOT NULL default '0',
  `versions` int(11) NOT NULL default '0',
  `edit_rights` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_page_settings`
-- 

INSERT INTO `astra_page_settings` VALUES (24, 1, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_pages`
-- 

CREATE TABLE `astra_pages` (
  `page_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `body` longtext NOT NULL,
  `added_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`page_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `astra_pages`
-- 

INSERT INTO `astra_pages` VALUES (1, 24, '<html>\r\n<head>\r\n<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">\r\n<title>Search</title>\r\n<link href="http://www.google.com/uds/css/gsearch.css" type="text/css" rel="stylesheet"/>\r\n<script src="http://www.google.com/uds/api?file=uds.js&amp;v=1.0&amp;key=ABQIAAAAy_d61TyTKJbshweQAd0F2RQSuvbTqsWZ_P--_2IajuYDHLaO7BQmeXBXs7pjH0x9Q0TVM-AYCgfzQw" type="text/javascript"></script>\r\n<script type="text/javascript" src="../libraries/search.js"></script>\r\n</head>\r\n<body>\r\n<div class="search-control" id="search_control_tabbed" height="100%" width="600px"></div>\r\n</body>\r\n</html>', 4, '2007-12-25 11:39:29');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_post_type`
-- 

CREATE TABLE `astra_post_type` (
  `type_key` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`type_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

-- 
-- Dumping data for table `astra_post_type`
-- 

INSERT INTO `astra_post_type` VALUES (1, 'Question');
INSERT INTO `astra_post_type` VALUES (2, 'Answer');
INSERT INTO `astra_post_type` VALUES (3, 'Agree');
INSERT INTO `astra_post_type` VALUES (4, 'Disagree');
INSERT INTO `astra_post_type` VALUES (5, 'Request for clarification');
INSERT INTO `astra_post_type` VALUES (6, 'Statement');
INSERT INTO `astra_post_type` VALUES (7, 'Opinion');
INSERT INTO `astra_post_type` VALUES (8, 'Greeting');
INSERT INTO `astra_post_type` VALUES (9, 'Response');
INSERT INTO `astra_post_type` VALUES (11, 'Suggestion');
INSERT INTO `astra_post_type` VALUES (12, 'Hot Tip');
INSERT INTO `astra_post_type` VALUES (13, 'Formal Task');
INSERT INTO `astra_post_type` VALUES (14, 'Waffle');
INSERT INTO `astra_post_type` VALUES (15, 'Comment');
INSERT INTO `astra_post_type` VALUES (16, 'News');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_post_user_links`
-- 

CREATE TABLE `astra_post_user_links` (
  `module_key` int(11) NOT NULL default '0',
  `post_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `read_status` tinyint(2) NOT NULL default '0',
  `flag_status` tinyint(2) NOT NULL default '0',
  `monitor_post` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `post_key` (`post_key`,`user_key`),
  KEY `module_key_idx` (`module_key`),
  KEY `user_key_idx` (`user_key`),
  KEY `post_key_idx` (`post_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_post_user_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_posts`
-- 

CREATE TABLE `astra_posts` (
  `post_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `parent_key` int(11) NOT NULL default '0',
  `thread_key` int(11) NOT NULL default '0',
  `entry_key` int(11) NOT NULL default '0',
  `multi_entry_key` int(11) NOT NULL default '0',
  `type_key` int(11) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `modified_by_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `subject` varchar(50) NOT NULL default '',
  `body` text NOT NULL,
  `extended_body` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_published` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `status_key` tinyint(4) NOT NULL default '0',
  `settings` tinyint(4) NOT NULL default '0',
  `attachment` varchar(40) NOT NULL default '',
  `unauth_name` varchar(60) default NULL,
  `unauth_email` varchar(70) default NULL,
  `unauth_url` varchar(80) default NULL,
  PRIMARY KEY  (`post_key`),
  KEY `module_key_idx` (`module_key`),
  KEY `parent_key_idx` (`parent_key`),
  KEY `thread_key_idx` (`thread_key`),
  FULLTEXT KEY `subject` (`subject`,`body`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_posts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_posts_auto_prompts`
-- 

CREATE TABLE `astra_posts_auto_prompts` (
  `post_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `date_prompted` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_actioned` datetime NOT NULL default '0000-00-00 00:00:00',
  `action_taken_key` tinyint(2) NOT NULL default '0',
  `prompted_by_key` int(11) NOT NULL default '0',
  UNIQUE KEY `post_key` (`post_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_posts_auto_prompts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_posts_autoprompt_actions`
-- 

CREATE TABLE `astra_posts_autoprompt_actions` (
  `action_key` tinyint(2) NOT NULL default '0',
  `name` varchar(45) NOT NULL default '',
  PRIMARY KEY  (`action_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_posts_autoprompt_actions`
-- 

INSERT INTO `astra_posts_autoprompt_actions` VALUES (1, 'Replied');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (2, 'Passed');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (3, 'Passed on to selected user');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (0, 'No Action Taken Yet');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (4, 'Prompting Stopped - Minimum Posts Reached');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (5, 'Prompting Stopped - Maximum Passes Reached');
INSERT INTO `astra_posts_autoprompt_actions` VALUES (6, 'No Response so flagged as pass');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_attempts`
-- 

CREATE TABLE `astra_qt_attempts` (
  `attempt_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `time_started` datetime NOT NULL default '0000-00-00 00:00:00',
  `time_finished` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `score` int(4) NOT NULL default '0',
  PRIMARY KEY  (`attempt_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `astra_qt_attempts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_attempts_data`
-- 

CREATE TABLE `astra_qt_attempts_data` (
  `attempt_key` int(11) NOT NULL auto_increment,
  `item_key` int(11) NOT NULL default '0',
  `response_ident` varchar(100) NOT NULL default '',
  `response_text` text NOT NULL,
  `correct` tinyint(1) NOT NULL default '0',
  UNIQUE KEY `attempt_key` (`attempt_key`,`item_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `astra_qt_attempts_data`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_categories`
-- 

CREATE TABLE `astra_qt_categories` (
  `category_key` int(11) NOT NULL auto_increment,
  `parent_key` int(11) NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `user_key` int(11) NOT NULL default '0',
  `space_key` int(11) NOT NULL default '0',
  PRIMARY KEY  (`category_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `astra_qt_categories`
-- 

INSERT INTO `astra_qt_categories` VALUES (1, 0, 'ffyhf', 4, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_category_item_links`
-- 

CREATE TABLE `astra_qt_category_item_links` (
  `category_key` int(11) NOT NULL default '0',
  `item_key` int(11) NOT NULL default '0',
  UNIQUE KEY `category_key` (`category_key`,`item_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_qt_category_item_links`
-- 

INSERT INTO `astra_qt_category_item_links` VALUES (1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_feedback`
-- 

CREATE TABLE `astra_qt_feedback` (
  `item_key` int(11) NOT NULL default '0',
  `feedback_linkref_id` varchar(100) NOT NULL default '',
  `mattext` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_qt_feedback`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_item`
-- 

CREATE TABLE `astra_qt_item` (
  `item_key` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `mattext` text NOT NULL,
  `response_type` varchar(5) NOT NULL default '',
  `render_type` varchar(20) NOT NULL default '',
  `rcardinality` varchar(10) NOT NULL default '',
  `rtiming` varchar(3) NOT NULL default '',
  `added_by_key` int(11) NOT NULL default '0',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`item_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `astra_qt_item`
-- 

INSERT INTO `astra_qt_item` VALUES (1, 'bmm', 'bmbm', 'lid', 'choice', 'Single', '', 4, 0, '2007-12-25 11:33:48', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_module_item_links`
-- 

CREATE TABLE `astra_qt_module_item_links` (
  `link_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `item_key` int(11) NOT NULL default '0',
  `sort_order` int(4) NOT NULL default '0',
  `score` float(3,1) NOT NULL default '0.0',
  PRIMARY KEY  (`link_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `astra_qt_module_item_links`
-- 

INSERT INTO `astra_qt_module_item_links` VALUES (1, 21, 1, 0, 0.0);
INSERT INTO `astra_qt_module_item_links` VALUES (2, 21, 1, 0, 0.0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_response`
-- 

CREATE TABLE `astra_qt_response` (
  `item_key` int(11) NOT NULL default '0',
  `mattext` text NOT NULL,
  `ident` varchar(100) NOT NULL default '',
  UNIQUE KEY `ident` (`ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_qt_response`
-- 

INSERT INTO `astra_qt_response` VALUES (1, 'True', '1_1');
INSERT INTO `astra_qt_response` VALUES (1, 'False', '1_2');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_qt_resprocessing`
-- 

CREATE TABLE `astra_qt_resprocessing` (
  `item_key` int(11) NOT NULL default '0',
  `response_ident` varchar(100) NOT NULL default '',
  `correct` tinyint(1) NOT NULL default '0',
  `score` float(3,1) NOT NULL default '0.0',
  `feedback_type` varchar(100) NOT NULL default '',
  `feedback_linkref_id` varchar(100) NOT NULL default '',
  UNIQUE KEY `item_key` (`item_key`,`response_ident`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_qt_resprocessing`
-- 

INSERT INTO `astra_qt_resprocessing` VALUES (1, '1_1', 1, 0.0, 'Response', '');
INSERT INTO `astra_qt_resprocessing` VALUES (1, '1_2', 0, 0.0, 'Response', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_quiz_settings`
-- 

CREATE TABLE `astra_quiz_settings` (
  `module_key` int(11) NOT NULL auto_increment,
  `type_key` tinyint(2) NOT NULL default '0',
  `open_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `close_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `attempts` tinyint(2) NOT NULL default '0',
  `shuffle_questions` tinyint(1) NOT NULL default '0',
  `shuffle_answers` tinyint(1) NOT NULL default '0',
  `grading_key` tinyint(2) NOT NULL default '0',
  `build_on_previous` tinyint(1) NOT NULL default '0',
  `show_feedback` tinyint(1) NOT NULL default '0',
  `feedback_attempts` tinyint(2) NOT NULL default '0',
  `show_correct` tinyint(1) NOT NULL default '0',
  `answer_attempts` tinyint(2) NOT NULL default '0',
  `minutes_allowed` int(3) NOT NULL default '0',
  KEY `module_keyIdx` (`module_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=30 ;

-- 
-- Dumping data for table `astra_quiz_settings`
-- 

INSERT INTO `astra_quiz_settings` VALUES (21, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 1, 1, 1, 0, 1, 0, 1, 0, 0);
INSERT INTO `astra_quiz_settings` VALUES (29, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 1, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_scorm`
-- 

CREATE TABLE `astra_scorm` (
  `module_key` int(11) NOT NULL default '0',
  `file_path` varchar(50) NOT NULL default '',
  `version` varchar(9) NOT NULL default '',
  `launch` int(10) unsigned NOT NULL default '0',
  `browsemode` tinyint(2) NOT NULL default '0',
  `auto` tinyint(1) unsigned NOT NULL default '0',
  `width` int(11) NOT NULL default '0',
  `height` int(11) NOT NULL default '0',
  PRIMARY KEY  (`module_key`),
  KEY `module_keyIdx` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_scorm`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_scorm_scoes`
-- 

CREATE TABLE `astra_scorm_scoes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `module_key` int(10) unsigned NOT NULL default '0',
  `manifest` varchar(255) NOT NULL default '',
  `organization` varchar(255) NOT NULL default '',
  `parent` varchar(255) NOT NULL default '',
  `identifier` varchar(255) NOT NULL default '',
  `launch` varchar(255) NOT NULL default '',
  `parameters` varchar(255) NOT NULL default '',
  `scormtype` varchar(5) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `prerequisites` varchar(200) NOT NULL default '',
  `maxtimeallowed` varchar(19) NOT NULL default '',
  `timelimitaction` varchar(19) NOT NULL default '',
  `datafromlms` varchar(255) NOT NULL default '',
  `masteryscore` varchar(200) NOT NULL default '',
  `next` tinyint(1) unsigned NOT NULL default '0',
  `previous` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `module_key` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_scorm_scoes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_scorm_scoes_track`
-- 

CREATE TABLE `astra_scorm_scoes_track` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL default '0',
  `module_key` int(10) NOT NULL default '0',
  `scoid` int(10) unsigned NOT NULL default '0',
  `element` varchar(255) NOT NULL default '',
  `value` longtext NOT NULL,
  `timemodified` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `track` (`userid`,`module_key`,`scoid`,`element`),
  KEY `userid` (`userid`),
  KEY `module_key` (`module_key`),
  KEY `scoid` (`scoid`),
  KEY `elemeny` (`element`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_scorm_scoes_track`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_server_settings`
-- 

CREATE TABLE `astra_server_settings` (
  `server_name` varchar(100) NOT NULL default '',
  `default_skin_key` int(11) NOT NULL default '1',
  `default_language` varchar(32) NOT NULL default 'en',
  `short_date_format` tinyint(4) NOT NULL default '0',
  `long_date_format` tinyint(4) NOT NULL default '0',
  `error_email` varchar(50) NOT NULL default '',
  `no_reply_email` varchar(50) NOT NULL default '',
  `email_type` varchar(8) NOT NULL default '',
  `sendmail_path` varchar(50) NOT NULL default '',
  `sendmail_args` varchar(50) NOT NULL default '',
  `email_host` varchar(50) NOT NULL default '',
  `email_port` int(3) NOT NULL default '0',
  `email_auth` varchar(5) NOT NULL default '',
  `email_username` varchar(50) NOT NULL default '',
  `email_password` varchar(20) NOT NULL default '',
  `max_file_upload_size` int(11) NOT NULL default '0',
  `keep_trash` int(3) NOT NULL default '0',
  `keep_stale_accounts` int(3) NOT NULL default '0',
  `show_emails` tinyint(1) NOT NULL default '0',
  `secure_server` tinyint(1) NOT NULL default '0',
  `secure_account_creation` tinyint(1) NOT NULL default '0',
  `account_creation_password` varchar(20) NOT NULL default '',
  `secret_hash` varchar(100) NOT NULL default '',
  `allow_tags` tinyint(1) NOT NULL default '0',
  `default_space_key` int(11) NOT NULL default '0',
  `global_gradebook` tinyint(1) NOT NULL default '0',
  `devolve_account_creation` tinyint(1) NOT NULL default '0',
  `self_delete` tinyint(1) NOT NULL default '0',
  `display_latest` tinyint(1) NOT NULL default '0',
  `usergroup_self_select` tinyint(1) NOT NULL default '0',
  `single_accounts` tinyint(1) NOT NULL default '0',
  `admin_set_skin` tinyint(1) NOT NULL default '0',
  `user_set_skin` tinyint(1) NOT NULL default '0',
  `short_urls` tinyint(1) NOT NULL default '0',
  `admins_add_spaces` tinyint(1) NOT NULL default '0',
  `user_spaces` tinyint(1) NOT NULL default '0',
  `enable_portfolios` tinyint(1) NOT NULL default '0',
  `options` smallint(5) unsigned NOT NULL default '1',
  `auth_type` varchar(25) NOT NULL default '',
  `proxy_server` varchar(100) NOT NULL default '',
  `proxy_port` int(4) NOT NULL default '0',
  `proxy_username` varchar(20) NOT NULL default '',
  `proxy_password` varchar(20) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_server_settings`
-- 

INSERT INTO `astra_server_settings` VALUES ('', 2, 'en', 0, 0, 'shafiqissani@gmail.com', 'shafiqissani@gmail.com', 'sendmail', '/usr/sbin/sendmail', '', 'localhost', 25, '1', '', '', 5242880, 30, 365, 1, 1, 2, '', '', 0, 1, 0, 2, 2, 1, 0, 1, 2, 2, 3, 0, 0, 1, 2, 'dbencrypt', '', 0, '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_sessions`
-- 

CREATE TABLE `astra_sessions` (
  `SESSKEY` varchar(32) NOT NULL default '',
  `EXPIRY` int(11) unsigned NOT NULL default '0',
  `EXPIREREF` varchar(64) default NULL,
  `DATA` text NOT NULL,
  PRIMARY KEY  (`SESSKEY`),
  KEY `EXPIRY` (`EXPIRY`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_sessions`
-- 

INSERT INTO `astra_sessions` VALUES ('65b4d1113e85b115d4792ef9ff19fc72', 1201452315, '', 'language%7Cs%3A2%3A%22en%22%3Bcurrent_space_key%7Cs%3A1%3A%221%22%3B');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_shared_item_comments`
-- 

CREATE TABLE `astra_shared_item_comments` (
  `comment_key` int(11) NOT NULL auto_increment,
  `shared_item_key` int(11) NOT NULL default '0',
  `parent_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `subject` varchar(50) NOT NULL default '',
  `body` text NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`comment_key`),
  KEY `shared_item_keyIdx` (`shared_item_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_shared_item_comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_shared_items`
-- 

CREATE TABLE `astra_shared_items` (
  `shared_item_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(100) NOT NULL default '',
  `description` text NOT NULL,
  `url` varchar(100) NOT NULL default '',
  `filename` varchar(50) NOT NULL default '',
  `file_path` varchar(11) NOT NULL default '',
  PRIMARY KEY  (`shared_item_key`),
  KEY `module_keyIdx` (`module_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for table `astra_shared_items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_sharing_settings`
-- 

CREATE TABLE `astra_sharing_settings` (
  `module_key` int(11) NOT NULL default '0',
  `file_path` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_sharing_settings`
-- 

INSERT INTO `astra_sharing_settings` VALUES (19, '54/19');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_skins`
-- 

CREATE TABLE `astra_skins` (
  `skin_key` int(11) NOT NULL auto_increment,
  `user_key` int(11) NOT NULL default '0',
  `template` varchar(20) NOT NULL default '',
  `scope_key` tinyint(2) NOT NULL default '0',
  `name` varchar(20) NOT NULL default '',
  `body_background` varchar(11) NOT NULL default '',
  `body_font` varchar(50) NOT NULL default '',
  `body_border_colour` varchar(11) NOT NULL default '',
  `outer_box_background` varchar(11) NOT NULL default '',
  `outer_box_border_colour` varchar(11) NOT NULL default '',
  `header_logo` varchar(255) NOT NULL default '',
  `header_logo_height` varchar(6) NOT NULL default '',
  `header_logo_width` varchar(6) NOT NULL default '',
  `header_height` varchar(6) NOT NULL default '',
  `header_background` varchar(11) NOT NULL default '',
  `header_border_colour` varchar(50) NOT NULL default '',
  `server_name_colour` varchar(11) NOT NULL default '',
  `inner_box_background` varchar(11) NOT NULL default '',
  `inner_box_border_colour` varchar(11) NOT NULL default '',
  `nav_background` varchar(11) NOT NULL default '',
  `nav_border_colour` varchar(11) NOT NULL default '',
  `content_background` varchar(11) NOT NULL default '',
  `content_border_colour` varchar(11) NOT NULL default '',
  `text_colour` varchar(11) NOT NULL default '',
  `colour1` varchar(11) NOT NULL default '',
  `colour2` varchar(11) NOT NULL default '',
  `raw_css` text NOT NULL,
  `version` int(10) NOT NULL default '0',
  PRIMARY KEY  (`skin_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `astra_skins`
-- 

INSERT INTO `astra_skins` VALUES (1, 1, 'default', 1, 'Default', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0);
INSERT INTO `astra_skins` VALUES (2, 4, 'classic', 1, 'shafiqSkin1', '#FAFDFF', 'Verdana, Arial, Helvetica, sans-serif', '#d9e6f7', '', '#d9e6f7', '', '', '', '', '', '#d9e6f7', '#314973', '#ffffff', '#d9e6f7', '#ffffff', '#d9e6f7', '#ffffff', '#d9e6f7', '#314973', '', '#00344f', '/* START inline_personal_box */\r\n#personalBox {\r\n	position: static; float:left; width:15.5em;\r\n	padding:0 0 .5em 7px; margin:0;\r\n}\r\n* html #personalBox {position:absolute; left:0em; top:auto;}\r\n#navigationBox {clear:left;}\r\n* html #navigationBox {top:auto; margin-top:6.1em;}\r\n/* END inline_personal_box */', 1201414754);
INSERT INTO `astra_skins` VALUES (3, 0, 'classic', 0, '111', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1201414208);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_sort_orders`
-- 

CREATE TABLE `astra_sort_orders` (
  `sort_order_key` tinyint(4) NOT NULL auto_increment,
  `sort_order` varchar(20) NOT NULL default '',
  `sort_sql` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`sort_order_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `astra_sort_orders`
-- 

INSERT INTO `astra_sort_orders` VALUES (1, 'Date Added', 'modules.date_added');
INSERT INTO `astra_sort_orders` VALUES (2, 'Date Added Desc', 'modules.date_added DESC');
INSERT INTO `astra_sort_orders` VALUES (3, 'Numeric', 'module_space_links.sort_order');
INSERT INTO `astra_sort_orders` VALUES (4, 'Alpha', 'modules.name');
INSERT INTO `astra_sort_orders` VALUES (5, 'Date Modified', 'modules.date_modified');
INSERT INTO `astra_sort_orders` VALUES (6, 'Date Modified Desc', 'modules.date_modified DESC');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_space_user_links`
-- 

CREATE TABLE `astra_space_user_links` (
  `space_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `access_level_key` tinyint(4) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `space_key` (`space_key`,`user_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_space_user_links`
-- 

INSERT INTO `astra_space_user_links` VALUES (2, 4, 2, '2007-09-25 11:29:26');
INSERT INTO `astra_space_user_links` VALUES (1, 4, 1, '2007-10-07 05:25:53');
INSERT INTO `astra_space_user_links` VALUES (1, 5, 3, '2008-01-27 06:18:28');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_spaces`
-- 

CREATE TABLE `astra_spaces` (
  `space_key` int(11) NOT NULL auto_increment,
  `module_key` int(11) NOT NULL default '0',
  `skin_key` int(11) NOT NULL default '0',
  `code` varchar(20) NOT NULL default '',
  `short_name` varchar(20) NOT NULL default '',
  `combine_names` tinyint(1) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `access_level_key` tinyint(4) NOT NULL default '0',
  `visibility_key` tinyint(4) NOT NULL default '0',
  `type_key` tinyint(1) NOT NULL default '0',
  `owned_by_key` int(11) NOT NULL default '0',
  `file_path` varchar(20) NOT NULL default '',
  `access_code` varchar(15) NOT NULL default '',
  `new_user_alert` varchar(5) NOT NULL default '',
  `header` mediumtext NOT NULL,
  `short_date_format_key` tinyint(4) NOT NULL default '0',
  `long_date_format_key` tinyint(4) NOT NULL default '0',
  `sort_order` int(3) NOT NULL default '0',
  `show_members` tinyint(1) NOT NULL default '1',
  `space_map` tinyint(1) NOT NULL default '0',
  `alt_home` varchar(255) NOT NULL default '',
  `welcome_message` mediumtext NOT NULL,
  PRIMARY KEY  (`space_key`),
  FULLTEXT KEY `name` (`name`,`short_name`,`code`,`description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- 
-- Dumping data for table `astra_spaces`
-- 

INSERT INTO `astra_spaces` VALUES (1, 0, 0, 'XXHOMEXX', '', 0, 'Home', 'Home', 3, 1, 0, 0, '', '', 'false', '', 1, 1, 0, 0, 0, '', '');
INSERT INTO `astra_spaces` VALUES (8, 15, 0, 'tKcQhv66', '', 0, 'College Website News', 'Entries of changes made to the college website.', 3, 0, 0, 0, '', '', 'false', '', 0, 0, 0, 1, 1, '', '');
INSERT INTO `astra_spaces` VALUES (9, 30, 1, '8Be9Tj93', '', 0, 'ghj', 'ghj', 1, 0, 1, 0, '', '', '', '', 0, 0, 0, 0, 0, '', 'gjh');
INSERT INTO `astra_spaces` VALUES (10, 31, 2, 'NZM8RO0b', '', 0, 'gj', 'fjg', 1, 0, 1, 0, '', '', '', '<img src="/astra/interact/images/smileys/regular_smile.gif" height="19" width="19">gbjhgjh <br>', 0, 0, 0, 0, 0, '', 'fgh');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_statistics`
-- 

CREATE TABLE `astra_statistics` (
  `space_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `date_accessed` datetime NOT NULL default '0000-00-00 00:00:00',
  `use_type` varchar(5) NOT NULL default '',
  `location` varchar(40) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_statistics`
-- 

INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:39:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:38:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:38:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:38:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:38:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:38:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:38:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:37:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:37:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:35:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:35:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:35:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:35:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:35:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:35:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:35:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:35:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:31:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:31:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:30:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:30:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:30:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:30:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:29:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:29:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:28:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:28:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:28:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:28:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:28:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:39:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:39:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:39:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:40:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:40:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:42:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:42:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:42:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:42:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:42:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:42:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:42:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:42:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:43:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:43:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:43:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:43:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:43:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:43:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:45:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:45:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:45:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:45:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:46:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:46:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:46:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:46:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:46:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:46:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:47:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:47:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:47:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:47:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:48:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:48:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:49:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:49:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:49:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:49:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:50:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:50:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 04:50:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 04:50:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 05:57:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 05:57:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 05:57:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2007-10-16 05:57:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2007-10-16 05:57:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 05:58:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 05:58:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 05:58:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 05:59:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 05:59:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-16 06:03:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 06:03:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-16 06:03:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 06:03:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 06:03:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-10-16 06:03:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 06:04:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 06:04:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 06:04:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 06:04:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-16 06:05:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 06:05:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-16 06:05:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 06:05:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 06:05:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 06:05:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:31:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:31:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:32:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:32:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:45:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:45:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:45:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:49:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:51:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:51:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:55:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:55:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 07:58:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 07:58:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:04:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:04:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:07:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:07:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:32:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:32:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:34:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:34:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:35:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:35:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:36:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:36:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:37:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:37:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:38:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:38:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:52:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:52:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:52:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:52:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:52:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:52:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:53:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:53:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:53:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:53:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:54:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:54:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:56:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:56:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:57:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:57:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:57:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:57:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:58:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:58:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 08:59:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 08:59:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:00:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:00:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:00:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:00:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:03:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:03:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:05:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:05:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:06:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:06:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:06:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:07:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:08:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:08:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:08:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:08:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:32:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:32:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:35:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:35:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:35:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:35:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:38:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:38:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:38:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:38:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:38:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:39:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:39:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:39:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:39:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:39:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:39:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:40:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:40:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:41:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:41:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 09:43:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 09:43:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 10:03:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 10:03:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 10:05:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 10:05:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 10:06:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 10:06:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 10:49:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 10:49:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-16 10:49:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-16 10:49:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:48:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:48:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:50:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:50:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:50:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:55:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-17 05:55:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:55:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:56:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:56:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:56:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:56:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:57:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:57:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:58:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:59:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 05:59:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 05:59:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 06:21:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 06:23:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 06:23:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 06:23:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 06:23:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 06:23:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 06:23:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 07:01:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 07:01:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:29:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:29:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:33:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:33:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:33:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:33:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:33:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:33:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:37:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:37:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 08:40:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 08:40:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:10:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:10:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:25:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:25:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:25:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:25:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:29:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:29:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:30:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:30:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:33:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:33:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 09:40:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 09:40:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 10:13:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 10:13:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 10:30:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 10:30:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-17 10:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-17 10:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 07:21:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 07:21:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 07:22:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 07:22:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 07:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 07:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:06:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:06:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:07:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:07:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:07:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:09:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:09:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:09:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:09:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:10:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:10:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:10:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:10:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:11:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:11:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:14:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:14:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-18 08:37:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-18 08:37:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:53:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 07:53:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 07:54:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:54:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:54:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:54:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 07:54:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:55:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 07:55:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 07:55:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:21:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:21:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:21:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:21:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:22:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:22:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:22:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:22:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:23:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:23:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:23:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:23:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:23:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:23:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:23:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:23:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 08:24:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 08:24:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 09:54:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-10-23 09:54:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-10-23 09:54:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-23 09:54:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-23 09:57:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-23 09:57:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-23 10:01:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-23 10:01:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 5, '2007-10-23 10:02:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 5, '2007-10-23 10:02:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:53:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 04:53:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:54:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 04:54:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:55:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:56:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 04:57:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:57:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:57:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 04:57:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:58:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 04:58:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 04:59:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-07 04:59:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:04:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:04:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:04:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:04:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:12:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:12:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:13:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:13:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:13:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:14:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:14:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:24:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:24:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:25:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:25:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 05:32:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-07 05:32:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-07 06:39:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-07 07:10:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-07 07:10:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:28:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:28:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:28:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:28:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:32:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:32:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:43:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:43:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:43:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:43:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:47:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:47:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:48:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:48:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:48:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:48:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:48:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:48:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:48:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:48:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:49:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:49:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:49:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:49:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:50:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:50:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:50:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:50:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:51:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:51:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:51:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:51:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:52:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:52:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:52:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:52:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:53:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:53:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:54:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:54:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:54:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:54:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:54:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:54:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:54:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:54:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:55:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:55:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:56:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:56:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:56:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:56:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:56:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:56:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:57:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:57:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 06:57:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:57:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 06:57:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:57:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:57:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:58:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:59:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:59:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:59:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:59:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 06:59:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 06:59:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:00:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:00:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:00:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:00:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:00:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:00:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:00:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:00:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:01:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:01:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:02:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:02:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:03:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:03:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:03:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:03:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:03:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:03:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:03:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:04:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:04:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:05:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:05:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:05:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:05:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:06:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:06:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:06:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:06:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:06:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:06:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:06:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:06:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:07:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:07:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:07:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:07:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:08:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:09:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:10:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 16, 4, '2007-11-08 07:10:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:12:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:12:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:12:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:12:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:14:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:14:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:14:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:14:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:14:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:14:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:14:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:14:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:14:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:16:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:16:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:16:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:16:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:16:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:16:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:17:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:17:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:17:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:19:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:19:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:19:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:19:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:20:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:20:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:20:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:20:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:20:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:20:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:21:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:21:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:21:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:21:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:21:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:21:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:21:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:21:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:22:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:22:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:22:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:22:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:22:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:22:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:22:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:22:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:22:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:23:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:23:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:23:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:23:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:23:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:23:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:23:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:24:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:24:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:24:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:24:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:24:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:24:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:24:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:24:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:24:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:25:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:25:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:25:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:25:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:25:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:25:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:26:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:26:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:26:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:26:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:26:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:26:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:26:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:26:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:26:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:26:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:26:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-08 07:26:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-11-08 07:26:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:27:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:27:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:27:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:28:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:28:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:28:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:28:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:28:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:28:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:31:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:31:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:31:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:35:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:35:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:35:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:35:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:35:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:36:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:36:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:36:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:38:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:38:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:38:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:41:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:41:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:41:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:41:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:41:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:42:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:42:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:42:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:43:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:43:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:43:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:44:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:44:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:44:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:45:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-08 07:45:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-08 07:45:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:04:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:04:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:04:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:04:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:04:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-11-20 04:04:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-11-20 04:05:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-20 04:05:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-11-20 04:06:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-17 07:46:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-17 07:46:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-17 07:47:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-17 07:47:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-17 07:47:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-17 07:47:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-17 07:47:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:11:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:11:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:13:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:13:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:13:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:14:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:14:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:15:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:15:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:15:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:15:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:15:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:15:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:16:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 17, 4, '2007-12-18 09:16:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 17, 4, '2007-12-18 09:18:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:24:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:24:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:24:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:24:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 09:24:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:25:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:25:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:25:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:26:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:27:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:27:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:27:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:34:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:37:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:37:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:37:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:42:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:42:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:42:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:50:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:50:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:50:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:50:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:50:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 09:54:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:54:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:54:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:54:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:54:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:54:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 09:54:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:56:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 09:56:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 10:03:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 10:03:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 10:03:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 10:03:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:03:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:03:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-18 10:41:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 10:41:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-18 10:41:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:42:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:45:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:45:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:45:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 10:46:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-18 10:46:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-18 10:46:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:36:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:36:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:36:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:36:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:36:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:36:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:37:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:37:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:58:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:58:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:58:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:58:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:58:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:58:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 10:59:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:59:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 10:59:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:01:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:01:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:02:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:02:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:03:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:03:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:04:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:04:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:04:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:05:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:05:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:05:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:05:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:06:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:06:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:06:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:10:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:10:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:10:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:10:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:10:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:10:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:11:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:11:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:11:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:11:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:11:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:11:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:12:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:12:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:12:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:12:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:12:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:12:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:12:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:12:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:12:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:12:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:13:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:13:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:13:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:13:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:13:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:14:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:14:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:16:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:16:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:18:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:18:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:19:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:19:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:19:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:24:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:24:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:24:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:25:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:25:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:25:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:25:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:25:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:25:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:25:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:25:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:26:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:26:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:28:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:28:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:28:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:28:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:28:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:28:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:29:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:29:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:29:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:29:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:29:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:29:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:30:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:30:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:30:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:30:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:31:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:31:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:31:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:31:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:31:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:31:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:31:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:38:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:38:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:39:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:39:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:39:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:39:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:39:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:39:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:39:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:39:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:39:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:39:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:39:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:39:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:41:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:41:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:41:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:41:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:41:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:41:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:41:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:42:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:42:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:42:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:42:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:42:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:42:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:43:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:43:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:43:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:43:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:43:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:44:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:44:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:44:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:44:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:45:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:45:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:45:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:45:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:45:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:45:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:45:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:46:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:46:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:46:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:46:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:46:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:46:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:46:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:46:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:46:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:46:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:46:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:46:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:47:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:47:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:47:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:47:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:47:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:47:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:48:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:48:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:48:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:48:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:48:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:48:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:48:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:48:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:48:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:48:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:48:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:48:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:48:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-24 11:48:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:48:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 11:48:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:49:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:49:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:49:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:49:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:50:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:50:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:50:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 11:50:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:50:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:50:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:51:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 11:51:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:00:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:01:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:01:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:01:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:01:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:01:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:02:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:02:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:03:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:04:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:04:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:04:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:04:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:05:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:05:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:05:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:06:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:06:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:09:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:09:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:09:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:12:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:12:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:12:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:13:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:13:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:13:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:13:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:14:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:14:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:14:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:14:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:14:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:14:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:14:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:14:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:14:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:15:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:16:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:16:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:16:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:17:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:17:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:17:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:17:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:18:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:18:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:18:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:18:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:18:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:18:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:19:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:20:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:20:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:22:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:23:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:23:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:23:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:23:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:23:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:23:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:23:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:23:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:24:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:24:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:24:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:24:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:25:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:27:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:27:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:27:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:27:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:27:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:29:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:30:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:30:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 17, 4, '2007-12-24 12:30:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:31:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:31:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:32:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:32:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:32:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:32:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2007-12-24 12:33:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:33:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:33:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:34:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:38:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:40:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:40:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:40:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:41:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:45:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:47:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:49:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:49:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:49:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:49:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:49:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:49:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:52:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:52:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:52:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:52:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:52:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:52:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:53:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:53:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:53:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:53:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:53:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:53:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:53:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:53:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:53:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:53:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:54:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:54:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:54:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:54:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:54:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:55:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:55:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:55:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:55:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:55:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:55:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:55:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:56:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:56:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:56:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:56:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:56:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:56:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:58:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:58:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:58:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:58:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:58:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:58:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:58:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:58:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:58:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:59:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:59:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:59:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:59:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:59:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:59:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 12:59:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:59:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 12:59:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:00:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:01:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:01:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:01:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:01:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:01:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:04:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:04:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:04:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:04:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:04:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:04:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:04:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:04:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:05:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:05:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:05:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:05:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:05:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:05:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:05:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:06:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:06:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:06:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:06:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:07:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:07:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:07:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:07:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:07:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:08:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:08:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:08:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:08:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:08:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:08:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:08:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:09:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:09:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:10:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:10:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:10:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:10:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:11:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:13:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:14:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:14:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:14:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:14:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:15:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:15:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:15:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:16:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:16:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:16:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:16:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:16:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:17:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:18:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:19:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:21:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:21:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:21:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:22:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:22:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:22:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:23:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:23:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:23:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:24:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:24:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:24:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:24:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:25:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:25:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:25:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:25:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:25:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:25:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:26:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:26:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:26:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:27:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:27:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:27:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:28:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:28:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:28:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:28:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:28:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:28:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:28:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:28:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:28:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:29:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:29:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:29:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:29:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:30:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:30:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:30:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:30:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:31:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:31:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:31:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:32:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:32:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:33:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:33:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:33:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:33:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:33:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:33:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:34:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:34:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:34:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:34:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:34:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:34:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:34:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:35:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:35:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:35:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:35:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:35:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:35:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:35:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:35:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:37:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:37:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:37:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:37:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:37:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:38:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:39:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:39:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:39:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:40:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:40:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:40:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:40:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:40:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:41:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:41:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:43:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:43:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:43:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:43:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:43:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:44:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:44:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:45:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:45:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:45:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:46:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:46:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-24 13:46:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-24 13:56:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:02:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:16:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:35:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:35:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:35:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:42:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:43:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:44:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:45:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:48:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:50:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:51:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:51:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:51:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:51:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:52:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:52:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:52:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:52:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:53:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:53:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:53:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:54:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:55:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:55:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:56:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:58:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 02:58:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:07:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:07:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:08:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:09:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:10:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:10:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:17:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:19:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:20:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:20:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:21:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:22:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:22:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:23:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:23:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:23:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:23:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:24:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:24:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:24:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:25:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:26:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:27:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:28:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:28:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:28:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:29:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:30:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:30:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:30:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:31:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:32:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:33:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:34:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:34:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:34:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:34:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:35:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:35:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:35:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:36:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:36:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:36:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:36:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:37:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:38:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:39:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:40:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:41:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:42:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:43:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:44:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:45:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:45:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:46:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:47:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:47:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:47:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:48:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:51:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:51:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:52:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:53:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:53:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:54:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:54:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:55:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:57:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:57:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:57:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:59:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 03:59:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:00:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:00:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:00:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:00:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:00:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:01:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:01:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:01:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:01:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:01:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:02:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:03:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:03:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:03:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:03:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:03:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:04:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:04:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:04:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:04:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:04:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:07:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:07:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:08:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:08:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:09:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:11:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:11:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:11:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:11:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:11:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:12:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:12:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:12:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:12:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:12:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:13:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:13:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:13:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:14:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:16:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:16:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:16:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:17:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:17:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:18:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:18:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:18:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:18:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:30:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:32:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:32:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:32:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:33:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:35:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:36:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:38:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:39:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:42:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:45:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:46:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:46:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:47:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:47:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:48:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:48:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:49:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:49:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:50:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:50:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:50:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:51:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:51:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:51:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:51:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:52:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:52:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:53:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:53:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:53:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:54:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:59:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 04:59:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:00:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:01:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:01:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:02:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:02:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:08:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:09:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:09:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:09:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:09:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:10:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:10:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:10:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:10:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:11:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:11:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:11:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:12:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:13:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:13:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:13:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:13:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:13:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:14:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:16:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:17:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:17:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:17:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:18:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:18:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:19:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:19:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:20:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:20:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:21:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:21:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:21:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:22:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:22:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:22:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:23:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 05:24:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 05:24:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 05:30:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:04:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:04:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:04:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:04:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:05:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:14:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:15:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:15:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:15:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:15:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:15:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:16:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:16:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:16:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:16:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:17:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:18:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:18:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:19:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:21:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:21:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:23:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:23:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:26:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:27:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:27:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:27:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:27:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:28:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:31:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:31:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:34:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 09:41:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:15:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:15:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:16:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:22:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:24:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:28:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 10:44:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:01:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:03:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:22:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:22:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:24:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:24:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-25 11:26:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2007-12-25 11:26:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:26:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:27:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:27:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 18, 4, '2007-12-25 11:27:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:28:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:28:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 19, 4, '2007-12-25 11:28:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 19, 4, '2007-12-25 11:29:29', 'post', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:29:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:30:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 20, 4, '2007-12-25 11:30:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 20, 4, '2007-12-25 11:31:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:31:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:32:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:33:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:34:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:35:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:37:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 22, 4, '2007-12-25 11:37:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:37:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 22, 4, '2007-12-25 11:37:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:38:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:38:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:38:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:38:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:39:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:39:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 24, 4, '2007-12-25 11:39:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:39:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:40:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 25, 4, '2007-12-25 11:40:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 25, 4, '2007-12-25 11:40:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:40:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:40:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:40:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:41:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 25, 4, '2007-12-25 11:41:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:41:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:42:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2007-12-25 11:42:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:42:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:42:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:42:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:42:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:42:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2007-12-25 11:43:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:43:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2007-12-25 11:43:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:43:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:43:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:43:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:44:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-25 11:44:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-25 11:44:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2007-12-25 11:44:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:44:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:44:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:45:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:45:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:45:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:46:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 28, 4, '2007-12-25 11:46:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:46:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:46:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2007-12-25 11:46:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-26 02:30:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-26 02:30:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 0, '2007-12-26 02:30:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-26 02:31:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-26 02:32:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2007-12-30 06:10:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-13 12:31:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:48:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:48:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:51:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:51:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:52:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:52:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:52:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:52:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:53:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:53:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:53:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:53:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:54:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:55:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:56:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:57:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:58:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:58:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:59:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:59:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:59:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 11:59:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:00:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:01:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:01:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:01:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:02:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:02:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:03:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:03:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:03:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:05:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:06:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:06:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:06:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:07:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:07:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:07:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:07:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:08:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:08:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:08:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:08:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:09:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:15:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:15:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:15:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:31:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:31:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:32:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:34:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:34:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:35:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:36:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:36:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:36:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:37:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:37:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:38:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:38:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:38:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:39:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:39:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:39:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:41:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:41:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:44:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:44:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:56:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:56:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:59:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 12:59:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:00:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:00:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:00:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:02:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:04:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:06:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:08:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:08:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:09:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:10:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:11:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:16:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:17:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:17:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:18:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:29:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:38:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:39:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 13:39:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-17 16:18:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-18 02:38:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-18 02:39:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-19 16:14:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-19 16:14:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-19 16:15:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-19 16:15:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-19 16:15:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:08:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:08:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:08:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2008-01-26 14:08:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:08:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:08:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:09:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:09:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:09:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:10:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:11:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:12:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:12:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:12:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:12:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:12:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:17:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:17:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:18:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:18:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:18:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:19:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:22:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:24:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:25:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:25:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:26:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:26:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:27:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:29:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:29:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:29:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:30:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:31:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:31:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:33:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:34:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:34:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:34:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:34:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:34:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:35:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:38:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:38:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 14:57:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:02:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:03:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:03:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:03:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:03:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:18:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:20:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2008-01-26 15:20:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:20:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:21:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2008-01-26 15:21:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:21:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:36:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:36:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:36:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:36:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:38:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:38:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:39:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:39:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:40:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:41:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:41:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:41:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:41:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:41:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:42:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:43:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:43:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:44:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:45:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:45:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:48:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:48:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:48:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:49:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 15:49:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:50:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:52:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:52:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:52:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:53:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:53:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 15:54:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:02:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:02:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:03:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:03:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:03:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:04:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:04:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:04:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:05:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:06:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:06:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:07:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:09:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:10:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:10:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:10:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:10:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:13:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:13:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:13:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-26 16:13:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-26 16:13:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:02:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:02:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:02:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:07:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:09:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:09:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:09:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:09:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:09:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:12:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:12:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:12:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:12:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:13:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:13:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:14:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:14:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:14:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:21:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:21:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:22:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:22:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:23:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:26:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:27:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:29:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:30:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:30:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:31:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:37:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:38:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:38:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:39:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:40:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:43:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:44:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:44:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:45:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:48:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:48:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 04:59:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:00:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:00:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:01:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:01:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:09:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:11:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:12:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:12:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:13:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:13:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:13:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:14:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:15:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:15:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:16:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:17:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:17:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:18:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:18:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:19:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:19:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:21:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:21:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:21:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:22:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:22:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:23:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:23:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:23:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:24:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:24:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:24:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:24:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:25:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:25:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:26:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:27:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:28:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:29:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:29:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:29:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:29:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:30:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:31:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:32:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:33:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:34:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:35:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:35:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:35:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:35:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:35:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:36:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:36:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:37:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:38:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:38:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:38:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:38:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:38:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:39:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:40:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:41:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:41:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:41:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:41:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:42:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:42:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:42:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:43:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:43:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:43:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:43:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:44:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:44:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:45:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:46:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:47:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:47:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:47:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:48:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:48:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:49:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:50:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:51:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:51:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:51:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:51:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:52:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:54:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:55:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:55:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 05:56:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:03:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:03:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:03:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:03:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:03:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:04:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:04:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:04:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:04:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:05:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:05:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:05:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:05:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:05:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:06:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:06:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:06:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:06:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:07:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:07:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:07:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:07:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:08:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:08:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:08:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:08:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:08:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:08:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:08:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:08:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:08:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:09:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:09:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:09:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:09:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:09:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:09:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:09:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:09:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:10:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:10:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:11:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:11:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:11:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:11:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:12:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:13:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:13:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:15:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:15:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 06:15:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:15:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:15:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:16:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:16:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:16:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:17:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:17:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2008-01-27 06:17:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:18:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 06:19:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:11:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:12:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 0, '2008-01-27 07:12:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:12:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:13:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:15:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:15:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:16:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:16:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:16:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:16:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:18:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:18:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:19:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:19:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:20:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:21:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:21:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:22:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:23:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:24:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:24:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:24:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:24:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:25:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:25:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:25:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:26:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:26:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:27:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:27:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:29:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:29:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:32:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:32:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:34:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:34:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:35:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:36:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:37:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:38:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:40:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:40:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:40:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:40:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:41:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:41:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:41:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:41:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:42:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:42:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:43:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:43:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:44:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:45:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:45:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:49:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:49:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:53:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:53:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:53:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:53:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:53:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2008-01-27 07:54:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:54:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:55:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:56:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:57:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 26, 4, '2008-01-27 07:57:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:58:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 07:59:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:59:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:59:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:59:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:59:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 07:59:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:00:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:00:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:00:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:00:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:00:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:00:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:00:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:00:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:00:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:01:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:01:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:01:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:06:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:51:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:51:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:51:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:51:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:54:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:57:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:57:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:57:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:57:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:58:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:59:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:59:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 08:59:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:59:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:59:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 08:59:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:00:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:01:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:01:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:01:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:03:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:04:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:05:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:05:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:05:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:06:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:06:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:07:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:07:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:07:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:08:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:08:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:08:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:12:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:12:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:12:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:13:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:13:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:13:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:23:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:24:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:24:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:24:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:25:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:26:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:27:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:31:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:32:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:33:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:34:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:35:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:36:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:36:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:36:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:39:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:40:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:41:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:42:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:43:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:44:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:45:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:46:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:46:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:46:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:48:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:49:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:50:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:52:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:53:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:53:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:53:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 09:54:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:54:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:54:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:54:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:55:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 09:56:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:00:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (9, 0, 4, '2008-01-27 10:00:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:00:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:00:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:01:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:01:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:01:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:01:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:01:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:02:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2008-01-27 10:02:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:03:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:04:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:04:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:05:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (10, 0, 4, '2008-01-27 10:05:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:05:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:05:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (8, 0, 4, '2008-01-27 10:06:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:06:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:06:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:06:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:06:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:06:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 10:07:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:07:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:08:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:08:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:08:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:11:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:11:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:12:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:12:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:12:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:14:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:14:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:14:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:15:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:15:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:15:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:15:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:15:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:16:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:17:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:17:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:18:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:18:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:18:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:20:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:22:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:23:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:24:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:25:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:26:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:27:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:28:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:29:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:30:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:30:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:30:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:30:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:32:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:32:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:32:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 6, '2008-01-27 10:32:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:33:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:34:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:34:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:40:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:40:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:40:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:42:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:42:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:42:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:43:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:43:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:43:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:45:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:45:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:45:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:46:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:46:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:46:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:49:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:49:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:49:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:51:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:51:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:51:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:52:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:52:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:52:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:53:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:53:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:53:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:55:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:55:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:55:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:56:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:56:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:56:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:57:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:57:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 10:57:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:02:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:02:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:02:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:07:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:07:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:07:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:08:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:08:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:08:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:11:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:11:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:11:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:17:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:26:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:26:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:27:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:28:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:28:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:28:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:29:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:30:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:31:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:31:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:31:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:32:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:33:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:34:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:35:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:36:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:37:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:37:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:37:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:37:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:37:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 4, '2008-01-27 11:56:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:56:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:56:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:56:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:56:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:57:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:57:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 11:57:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:01:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:06:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 12:18:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:33:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:33:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:33:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:47', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:35:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:36:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:37:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:39:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:29', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:40:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:41:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:41:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:41:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:45:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:54:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:54:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:54:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:55:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:55:32', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 13:55:33', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:16:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:16:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:16:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:39:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:40:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:41:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:52', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:42:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:16', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:36', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:49', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:43:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:12', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:38', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:44:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:30', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:45:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:46', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:46:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:11', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:47:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:10', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:48:59', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:08', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:09', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:45', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:50', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:51', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:49:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:54:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:54:03', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:54:04', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:07', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:27', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 14:56:28', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:13:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:13:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:13:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:19', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:15:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:01', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:02', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:48:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:48:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:48:44', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:49:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:49:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:49:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:20', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 15:57:48', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:03:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:03:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:03:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:04:26', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:05:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:05:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:05:00', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:07:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:07:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:07:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:23', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:24', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:25', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:53', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:08:54', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:10:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:10:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:10:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:21', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:22', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:39', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:11:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:05', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:06', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:40', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:12:41', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:42', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:43', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:13:56', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:17', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:18', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:34', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:35', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:16:55', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:17:31', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:37', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:57', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:18:58', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:19:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:19:13', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:19:14', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:21:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:21:15', 'read', '127.0.0.1');
INSERT INTO `astra_statistics` VALUES (1, 0, 0, '2008-01-27 16:21:15', 'read', '127.0.0.1');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_tag_links`
-- 

CREATE TABLE `astra_tag_links` (
  `tag_key` int(11) NOT NULL default '0',
  `user_key` int(11) NOT NULL default '0',
  `module_key` int(11) NOT NULL default '0',
  `entry_key` int(11) NOT NULL default '0',
  `url_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `tag_key` (`tag_key`,`user_key`,`module_key`,`entry_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_tag_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_tagged_urls`
-- 

CREATE TABLE `astra_tagged_urls` (
  `url_key` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL default '',
  `heading` varchar(100) NOT NULL default '',
  `note` text NOT NULL,
  `added_for_key` int(11) NOT NULL default '0',
  `added_by_key` int(11) NOT NULL default '0',
  `space_key` int(11) NOT NULL default '0',
  `group_key` int(11) NOT NULL default '0',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by_key` int(11) NOT NULL default '0',
  `date_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`url_key`),
  KEY `added_for_keyIdx` (`added_for_key`),
  KEY `space_keyIdx` (`space_key`),
  KEY `group_keyIdx` (`group_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_tagged_urls`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_tags`
-- 

CREATE TABLE `astra_tags` (
  `tag_key` int(11) NOT NULL auto_increment,
  `text` varchar(50) NOT NULL default '0',
  PRIMARY KEY  (`tag_key`),
  UNIQUE KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_tags`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_user_groups`
-- 

CREATE TABLE `astra_user_groups` (
  `user_group_key` int(6) NOT NULL auto_increment,
  `group_name` varchar(50) NOT NULL default '',
  `account_creation_password` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`user_group_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `astra_user_groups`
-- 

INSERT INTO `astra_user_groups` VALUES (1, 'Staff', '');
INSERT INTO `astra_user_groups` VALUES (2, 'Students', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_user_messages`
-- 

CREATE TABLE `astra_user_messages` (
  `message_key` int(11) NOT NULL auto_increment,
  `added_by_key` int(11) NOT NULL default '0',
  `added_for_key` int(11) NOT NULL default '0',
  `message` varchar(255) NOT NULL default '',
  `status_key` tinyint(1) NOT NULL default '0',
  `time` int(11) NOT NULL default '0',
  PRIMARY KEY  (`message_key`),
  KEY `user_key_idx` (`added_for_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `astra_user_messages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `astra_user_statuses`
-- 

CREATE TABLE `astra_user_statuses` (
  `account_status_key` tinyint(4) NOT NULL auto_increment,
  `name` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`account_status_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `astra_user_statuses`
-- 

INSERT INTO `astra_user_statuses` VALUES (1, 'Active');
INSERT INTO `astra_user_statuses` VALUES (2, 'Disabled');
INSERT INTO `astra_user_statuses` VALUES (3, 'To be deleted');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_user_usergroup_links`
-- 

CREATE TABLE `astra_user_usergroup_links` (
  `user_key` int(11) NOT NULL default '0',
  `user_group_key` int(11) NOT NULL default '0',
  UNIQUE KEY `user_key` (`user_key`,`user_group_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_user_usergroup_links`
-- 

INSERT INTO `astra_user_usergroup_links` VALUES (4, 1);
INSERT INTO `astra_user_usergroup_links` VALUES (16, 1);
INSERT INTO `astra_user_usergroup_links` VALUES (17, 1);
INSERT INTO `astra_user_usergroup_links` VALUES (18, 1);
INSERT INTO `astra_user_usergroup_links` VALUES (19, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_users`
-- 

CREATE TABLE `astra_users` (
  `user_key` int(11) NOT NULL auto_increment,
  `username` varchar(20) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `user_id_number` varchar(20) NOT NULL default '',
  `first_name` varchar(50) NOT NULL default '',
  `last_name` varchar(50) NOT NULL default '',
  `prefered_name` varchar(30) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `details` mediumtext NOT NULL,
  `level_key` tinyint(4) NOT NULL default '0',
  `photo` varchar(15) NOT NULL default '',
  `file_path` varchar(20) NOT NULL default '',
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `use_count` int(11) NOT NULL default '0',
  `last_use` datetime NOT NULL default '0000-00-00 00:00:00',
  `account_status` tinyint(4) NOT NULL default '0',
  `language_key` varchar(32) NOT NULL default '',
  `auto_load_editor` tinyint(1) NOT NULL default '1',
  `read_posts_flag` tinyint(1) NOT NULL default '0',
  `skin_key` int(11) NOT NULL default '1',
  `address` mediumtext NOT NULL,
  `phone_no` varchar(15) NOT NULL,
  `branch` varchar(10) NOT NULL,
  `attendance` float NOT NULL,
  `backlogs` int(2) NOT NULL,
  PRIMARY KEY  (`user_key`),
  UNIQUE KEY `username` (`username`),
  KEY `usernameIdx` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- 
-- Dumping data for table `astra_users`
-- 

INSERT INTO `astra_users` VALUES (2, '_interact_deleted', 'xyz123', '', 'Deleted', 'User', '', 'deleted@example.com', 'Account used when deleting users from system - do not remove', 4, '', '', '2007-09-25 10:49:10', 0, '0000-00-00 00:00:00', 0, 'en', 1, 0, 0, '', '', '', 0, 0);
INSERT INTO `astra_users` VALUES (3, '_interact_unauth', 'xyz123', '', '', 'User', '', 'unauthorised@example.com', 'Account used by Interact for unauthorised (not logged in) content - do not remove', 4, '', '', '2007-09-25 10:49:10', 0, '0000-00-00 00:00:00', 0, 'en', 1, 0, 0, '', '', '', 0, 0);
INSERT INTO `astra_users` VALUES (4, 'admin', 'fc035cd4691e52933ef57412eb29d399', '', 'Administrator', '', '', 'sendmailastra@gmail.com', '', 1, '4.jpg', '46/4', '2007-09-25 11:24:10', 67, '2008-01-27 11:37:50', 1, 'en', 1, 0, 0, '', '', '', 0, 0);
INSERT INTO `astra_users` VALUES (6, '_interact_x', 'fc035cd4691e52933ef57412eb29d399', '', 'Execute', 'User', '', 'execute@example.com', 'Account used when executing users from system - do not remove', 1, '', '', '2007-09-25 11:24:10', 0, '2008-01-27 10:12:24', 0, 'en', 1, 0, 0, '', '', '', 0, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_version`
-- 

CREATE TABLE `astra_version` (
  `version` int(11) NOT NULL default '0',
  `release_name` varchar(10) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_version`
-- 

INSERT INTO `astra_version` VALUES (2007071601, '9.9');

-- --------------------------------------------------------

-- 
-- Table structure for table `astra_weblinks`
-- 

CREATE TABLE `astra_weblinks` (
  `module_key` int(11) NOT NULL default '0',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`module_key`),
  KEY `module_keyIdx` (`module_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `astra_weblinks`
-- 

