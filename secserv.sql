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

 Date: 03/30/2017 10:33:21 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET FOREIGN_KEY_CHECKS = 1;