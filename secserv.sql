/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MariaDB
 Source Server Version : 100118
 Source Host           : localhost
 Source Database       : secserv

 Target Server Type    : MariaDB
 Target Server Version : 100118
 File Encoding         : utf-8

 Date: 04/01/2017 15:23:52 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `secserv_approval_sheet`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_approval_sheet`;
CREATE TABLE `secserv_approval_sheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_month_year_id` int(11) DEFAULT NULL,
  `client_department_id` int(11) DEFAULT NULL,
  `client_service_id` int(11) DEFAULT NULL,
  `is_overtime_record` tinyint(4) DEFAULT NULL,
  `d1` decimal(5,2) DEFAULT NULL,
  `d2` decimal(5,2) DEFAULT NULL,
  `d3` decimal(5,2) DEFAULT NULL,
  `d4` decimal(5,2) DEFAULT NULL,
  `d5` decimal(5,2) DEFAULT NULL,
  `d6` decimal(5,2) DEFAULT NULL,
  `d7` decimal(5,2) DEFAULT NULL,
  `d8` decimal(5,2) DEFAULT NULL,
  `d9` decimal(5,2) DEFAULT NULL,
  `d10` decimal(5,2) DEFAULT NULL,
  `d11` decimal(5,2) DEFAULT NULL,
  `d12` decimal(5,2) DEFAULT NULL,
  `d13` decimal(5,2) DEFAULT NULL,
  `d14` decimal(5,2) DEFAULT NULL,
  `d15` decimal(5,2) DEFAULT NULL,
  `d16` decimal(5,2) DEFAULT NULL,
  `d17` decimal(5,2) DEFAULT NULL,
  `d18` decimal(5,2) DEFAULT NULL,
  `d19` decimal(5,2) DEFAULT NULL,
  `d20` decimal(5,2) DEFAULT NULL,
  `d21` decimal(5,2) DEFAULT NULL,
  `d22` decimal(5,2) DEFAULT NULL,
  `d23` decimal(5,2) DEFAULT NULL,
  `d24` decimal(5,2) DEFAULT NULL,
  `d25` decimal(5,2) DEFAULT NULL,
  `d26` decimal(5,2) DEFAULT NULL,
  `d27` decimal(5,2) DEFAULT NULL,
  `d28` decimal(5,2) DEFAULT NULL,
  `d29` decimal(5,2) DEFAULT NULL,
  `d30` decimal(5,2) DEFAULT NULL,
  `d31` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_attendance`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_attendance`;
CREATE TABLE `secserv_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `labour_id` int(11) NOT NULL,
  `client_month_year_id` int(11) NOT NULL,
  `client_department_id` int(11) DEFAULT NULL,
  `client_service_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `unit` varchar(255) NOT NULL,
  `units_work` decimal(5,2) NOT NULL,
  `overtime_units_work` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `secserv_billing_service`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_billing_service`;
CREATE TABLE `secserv_billing_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client`;
CREATE TABLE `secserv_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `service_tax` decimal(5,2) DEFAULT NULL,
  `generate_mannual_invoice` tinyint(4) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `client_department_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client_department`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_department`;
CREATE TABLE `secserv_client_department` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `default_client_service_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client_monthyear_approved_data`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_monthyear_approved_data`;
CREATE TABLE `secserv_client_monthyear_approved_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_month_year_id` int(11) DEFAULT NULL,
  `client_department_id` int(11) DEFAULT NULL,
  `client_service_id` int(11) DEFAULT NULL,
  `units_approved` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client_monthyear_invoice_detail`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_monthyear_invoice_detail`;
CREATE TABLE `secserv_client_monthyear_invoice_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_month_year_id` int(11) DEFAULT NULL,
  `billing_service_id` int(11) DEFAULT NULL,
  `units` decimal(10,2) DEFAULT NULL,
  `rate` decimal(10,2) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client_monthyear_record`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_monthyear_record`;
CREATE TABLE `secserv_client_monthyear_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `month_year` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_client_service`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_service`;
CREATE TABLE `secserv_client_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `billing_service_id` int(11) DEFAULT NULL,
  `invoice_base` varchar(255) DEFAULT NULL,
  `client_shift_hours` varchar(255) DEFAULT NULL,
  `invoice_rate` varchar(255) DEFAULT NULL,
  `payment_base` varchar(255) DEFAULT NULL,
  `labour_shift_hours` varchar(255) DEFAULT NULL,
  `payment_rate` varchar(255) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `secserv_labour`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_labour`;
CREATE TABLE `secserv_labour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `default_client_id` int(11) DEFAULT NULL,
  `default_client_department_id` int(11) DEFAULT NULL,
  `default_client_service_id` int(11) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `labour_shift_hours` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;
