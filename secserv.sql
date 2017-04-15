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

 Date: 04/01/2017 18:32:32 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

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
  `shift_units_work` decimal(14,2) NOT NULL,
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


-- update query for client month record
ALTER TABLE `secserv_client_monthyear_record` ADD COLUMN `invoice_no` int(11);
ALTER TABLE `secserv_client_monthyear_record` ADD COLUMN `invoice_date` date DEFAULT NULL;
ALTER TABLE `secserv_client_monthyear_record` ADD COLUMN `service_tax` decimal(5,2) DEFAULT NULL;

-- client record updated
ALTER TABLE `secserv_client` ADD COLUMN `invoice_layout_id` int(11) DEFAULT NULL;
/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : printonclick

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-04-14 15:27:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `secserv_layout`
-- ----------------------------
DROP TABLE IF EXISTS `secserv_layout`;
CREATE TABLE `secserv_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `master` text,
  `detail` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of secserv_layout
-- ----------------------------
INSERT INTO `secserv_layout` VALUES ('1', 'security', '<table style=\"width: 100.567%; font-family: times new roman,times,serif;\">\r\n<tbody>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px; text-align: center;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> <span style=\"font-family: times new roman,times,serif;\"><strong>INVOICE</strong></span></td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"><strong> <span style=\"font-family: times new roman,times,serif;\">Tin No : 989898765454</span></strong></td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> <span style=\"font-family: times new roman,times,serif;\">Our Banker :</span></td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">  Union Bank Of India, Main Branch, Gujrat</td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">  A/c No : 8787787898989</td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">  IFSC Code : UBIN0531014</td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 100%; border: 1px solid #6e6e6e;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 322px;\">\r\n<table style=\"width: 100%;\">\r\n<tbody>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"><span style=\"font-family: times new roman,times,serif;\">To</span>,</td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"font-family: times new roman,times,serif; height: 14px;\"><strong>{$client}{$name}</strong></td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">{$client_address}</td>\r\n</tr>\r\n<tr style=\"height: 10.6333px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10.6333px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">Emails: {$email}</td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">Contacts: {$mobile_no}</td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">PIN No.: {$client_pan_no}</td>\r\n</tr>\r\n<tr style=\"height: 10px;\">\r\n<td style=\"font-size: 8px; font-family: times new roman,times,serif; height: 10px;\">TIN No.: {$client_tin_no}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td style=\"border-left: 1px solid #6e6e6e; width: 217px;\">\r\n<table style=\"width: 100%;\">\r\n<tbody>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px; text-align: center;\"> </td>\r\n<td style=\"height: 14px; text-align: center;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px; text-align: center;\"> <span style=\"font-family: times new roman,times,serif;\"><strong>Invoice No :</strong> {$id}</span></td>\r\n<td style=\"height: 14px; text-align: center;\"><span style=\"font-family: times new roman,times,serif;\"><strong>Date : </strong>{$created_at}</span></td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px; text-align: center;\"> </td>\r\n<td style=\"height: 14px; text-align: center;\"> </td>\r\n</tr>\r\n<tr style=\"height: 14px;\">\r\n<td style=\"height: 14px;\"> </td>\r\n<td style=\"height: 14px;\"> </td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 100%; border: 1px solid #6e6e6e;\" cellpadding=\"0\">\r\n<tbody>\r\n<tr style=\"width: 100%;\">\r\n<td>{$item_info}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 100%; border-bottom: 1px solid #6e6e6e;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 59.35%; border-left: 1px solid #6e6e6e;\">\r\n<table style=\"width: 100%;\">\r\n<tbody>\r\n<tr>\r\n<td> </td>\r\n</tr>\r\n<tr>\r\n<td> </td>\r\n</tr>\r\n<tr>\r\n<td> </td>\r\n</tr>\r\n<tr>\r\n<td> </td>\r\n</tr>\r\n<tr>\r\n<td><strong><span style=\"font-family: times new roman,times,serif;\">Amount In Words :</span></strong><span style=\"font-family: times new roman,times,serif;\">{$amountinwords}</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td style=\"width: 40.62%;\">\r\n<table style=\"width: 101.40%; border: 1px solid #6e6e6e; padding: 4px;\">\r\n<tbody>\r\n<tr style=\"height: 20px;\">\r\n<td style=\"height: 22px; border-bottom: 1px solid #6e6e6e; border-right: 1px solid #6e6e6e; text-align: right;\"><strong><span style=\"font-family: times new roman,times,serif;\">Gross Amount:</span></strong></td>\r\n<td style=\"height: 22px; border-bottom: 1px solid #6e6e6e; text-align: right;\"><span style=\"font-size: 10px;\">{$currency} {$currency_id}<span style=\"font-family: times new roman,times,serif;\">{$gross_amount}</span></span></td>\r\n</tr>\r\n<tr style=\"height: 20px;\">\r\n<td style=\"height: 22px; border-bottom: 1px solid #6e6e6e; border-right: 1px solid #6e6e6e; text-align: right;\"><strong><span style=\"font-family: times new roman,times,serif;\">Discount:</span></strong></td>\r\n<td style=\"height: 22px; border-bottom: 1px solid #6e6e6e; text-align: right;\"><span style=\"font-size: 10px;\">{$currency} {$currency_id}<span style=\"font-family: times new roman,times,serif;\">{$discount_amount}</span></span></td>\r\n</tr>\r\n<tr style=\"height: 20px;\">\r\n<td style=\"height: 22px; border-bottom: 1px solid #6e6e6e; border-right: 1px solid #6e6e6e; text-align: right;\"><strong><span style=\"font-family: times new roman,times,serif;\">Net Amount:</span></strong></td>\r\n<td style=\"height: 22px; text-align: right;\"><span style=\"font-size: 10px;\">{$currency} {$currency_id}<span style=\"font-family: times new roman,times,serif;\">{$net_amount}</span></span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"height: 5px;\">\r\n<tbody>\r\n<tr>\r\n<td> </td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<table style=\"width: 100.057%;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 60.%;\">\r\n<table style=\"width: 100%;\">\r\n<tbody>\r\n<tr>\r\n<td>\r\n<p><span style=\"font-family: times new roman,times,serif;\">Terms and conditions :</span><br /><br />term and condition 1<br />term and condition 1<br />term and condition 1<span style=\"font-family: times new roman,times,serif; font-size: 6px;\"> <br /><br /></span></p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td style=\"text-align: right; width: 40%;\">\r\n<table style=\"width: 100%;\">\r\n<tbody>\r\n<tr>\r\n<td colspan=\"2\"><span style=\"font-family: times new roman,times,serif;\">For : Shiv Security Services<br /></span></td>\r\n</tr>\r\n<tr style=\"height: 100px;\">\r\n<td style=\"height: 30px;\" colspan=\"2\"> </td>\r\n</tr>\r\n<tr>\r\n<td style=\"text-align: right;\"> <span style=\"font-family: times new roman,times,serif;\">Customer Signature</span></td>\r\n<td style=\"text-align: right;\"><span style=\"font-family: times new roman,times,serif;\"> Authorized Signatory</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>', '<table style=\"width: 102%; font-family: times new roman,times,serif; padding: 5px;\"><!--{header}{cols}{col}-->\r\n<thead>\r\n<tr style=\"border: 0.9px solid #6e6e6e;\">\r\n<td style=\"width: 120px; font-size: 8px; border: 1px solid #6e6e6e;\"><span style=\"font-size: 10px;\"><strong>Particular</strong></span></td>\r\n<td style=\"font-size: 8px; text-align: center; width: 45px; border: 1px solid #6e6e6e;\"><span style=\"font-size: 10px;\"><strong>Qty</strong></span></td>\r\n<td style=\"font-size: 8px; text-align: center; border: 1px solid #6e6e6e;\"><span style=\"font-size: 10px;\"><strong>Rate</strong></span></td>\r\n<td style=\"font-size: 8px; text-align: right; border: 1px solid #6e6e6e;\"><strong><span style=\"font-size: 10px;\">Amount</span> <br /></strong><span style=\"font-size: 8px;\">(Exluding Tax)</span></td>\r\n</tr>\r\n</thead>\r\n<!--{/}{/}{/}--> <!--{$subheader}--> <!--{rows}{row}{cols}-->\r\n<tbody>\r\n<tr>\r\n<td style=\"text-align: left; width: 120px; border: 1px solid #6e6e6e;\"><span style=\"font-size: 11px;\">{$billing_service}</span></td>\r\n<td style=\"font-size: 8px; text-align: center; width: 45px; border: 1px solid #6e6e6e;\"><span style=\"font-size: 11px;\">{$units}</span></td>\r\n<td style=\"font-size: 8px; text-align: center; border: 1px solid #6e6e6e;\"><span style=\"font-size: 11px;\">{$rate}</span></td>\r\n<td style=\"font-size: 8px; text-align: right; border: 1px solid #6e6e6e;\"><span style=\"font-size: 11px;\">{$amount}</span></td>\r\n</tr>\r\n</tbody>\r\n<!-- {/cols}{/row}{/rows}--></table>\r\n<p>{not_found}{$not_found_message}{/}</p>');


-- COLUMNM FIELDS ADDED
ALTER TABLE `secserv_labour` ADD COLUMN `address` text, 
ADD COLUMN `dob` date,
ADD COLUMN `gender` VARCHAR(255),
ADD COLUMN `mobile_no` VARCHAR(255),
ADD COLUMN `email_id` VARCHAR(255),
ADD COLUMN `guardian_name` VARCHAR(255),
ADD COLUMN `bank_name` VARCHAR(255),
ADD COLUMN `bank_account_no` VARCHAR(255),
ADD COLUMN  `bank_ifsc_code` VARCHAR(255),
ADD COLUMN  `bank_branch` VARCHAR(255);

--  COLUMNS ADDED IN CLIENT MODEL
ALTER TABLE `secserv_client`
ADD COLUMN `address` text, 
ADD  COLUMN`owner_name` VARCHAR(255),
ADD COLUMN `mobile_no` VARCHAR(255),
ADD COLUMN `email_id` VARCHAR(255),
ADD COLUMN `tin_no` VARCHAR(255);