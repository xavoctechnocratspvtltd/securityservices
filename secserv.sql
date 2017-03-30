/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : printonclick

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-03-30 12:57:40
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `secserv_attendance`
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
  `unit_work` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of secserv_attendance
-- ----------------------------
INSERT INTO `secserv_attendance` VALUES ('1', '2', '3', '3', '4', '2017-03-30 00:00:00', 'shift', '190');

-- ----------------------------
-- Table structure for `secserv_billing_service`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_billing_service`;
CREATE TABLE `secserv_billing_service` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_billing_service
-- ----------------------------
INSERT INTO `secserv_billing_service` VALUES ('7', 'H/W', '5', '2017-03-30 11:52:41');
INSERT INTO `secserv_billing_service` VALUES ('8', 'S/W', '5', '2017-03-30 11:52:47');
INSERT INTO `secserv_billing_service` VALUES ('9', 'H/W doc', '5', '2017-03-30 11:52:55');

-- ----------------------------
-- Table structure for `secserv_client`
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_client
-- ----------------------------
INSERT INTO `secserv_client` VALUES ('2', 'Rakesh', '15.00', '1', null, null, '5', '2017-03-30 11:53:36', 'Active');

-- ----------------------------
-- Table structure for `secserv_client_department`
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_client_department
-- ----------------------------
INSERT INTO `secserv_client_department` VALUES ('3', 'Dahi Ladies ', '2', '4', '5', '2017-03-30 12:03:59');
INSERT INTO `secserv_client_department` VALUES ('4', 'Dairy Security', '2', '4', '5', '2017-03-30 12:04:16');
INSERT INTO `secserv_client_department` VALUES ('5', 'Ladies Night', '2', '5', '5', '2017-03-30 12:04:30');

-- ----------------------------
-- Table structure for `secserv_client_monthyear_record`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_client_monthyear_record`;
CREATE TABLE `secserv_client_monthyear_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_client_monthyear_record
-- ----------------------------
INSERT INTO `secserv_client_monthyear_record` VALUES ('3', '02-2017', '2', '5', '2017-03-30 11:55:49');
INSERT INTO `secserv_client_monthyear_record` VALUES ('4', '03-2017', '2', '5', '2017-03-30 11:56:03');

-- ----------------------------
-- Table structure for `secserv_client_service`
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_client_service
-- ----------------------------
INSERT INTO `secserv_client_service` VALUES ('4', 'H/W', '2', '7', 'Shift', '9', '300', 'Hour', '8', '20', '5', '2017-03-30 11:59:37');
INSERT INTO `secserv_client_service` VALUES ('5', 'S/W', '2', '8', 'Shift', '12', '350', 'Hour', '12', '25', '5', '2017-03-30 12:03:11');

-- ----------------------------
-- Table structure for `secserv_labour`
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of secserv_labour
-- ----------------------------
INSERT INTO `secserv_labour` VALUES ('2', 'labour 1', '2', '4', '4', '1', '5', '2017-03-30 12:05:28');
INSERT INTO `secserv_labour` VALUES ('3', 'labour 2', '2', '4', '5', '1', '5', '2017-03-30 12:05:52');
INSERT INTO `secserv_labour` VALUES ('4', 'Labour 3', '2', '5', '5', '1', '5', '2017-03-30 12:06:09');
