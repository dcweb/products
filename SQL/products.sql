/*
Navicat MySQL Data Transfer

Source Server         : Combell_iis
Source Server Version : 50623
Source Host           : 178.208.48.50:3306
Source Database       : xsetup

Target Server Type    : MYSQL
Target Server Version : 50623
File Encoding         : 65001

Date: 2017-10-05 15:37:34
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `products`
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `eancode` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `volume` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `volume_unit_id` int(11) unsigned DEFAULT NULL,
  `new` tinyint(11) DEFAULT '0',
  `discontinued` tinyint(4) DEFAULT '0',
  `matter_id` int(11) unsigned DEFAULT NULL,
  `online` tinyint(4) DEFAULT '1',
  `admin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_volume_unit_id` (`volume_unit_id`) USING BTREE,
  KEY `FK_matterid` (`matter_id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`volume_unit_id`) REFERENCES `products_volume_units` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products
-- ----------------------------

-- ----------------------------
-- Table structure for `products_attachments`
-- ----------------------------
DROP TABLE IF EXISTS `products_attachments`;
CREATE TABLE `products_attachments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned DEFAULT NULL,
  `language_id` int(11) unsigned DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `admin` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_prdattachmentlanguage` (`language_id`),
  KEY `FK_prdattachproduct` (`product_id`),
  CONSTRAINT `products_attachments_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `products_attachments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of products_attachments
-- ----------------------------

-- ----------------------------
-- Table structure for `products_categories_language`
-- ----------------------------
DROP TABLE IF EXISTS `products_categories_language`;
CREATE TABLE `products_categories_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `depth` int(11) DEFAULT NULL,
  `language_id` int(11) unsigned DEFAULT NULL,
  `sort_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `_oldid` int(11) DEFAULT NULL COMMENT 'remove when finished',
  `_oldtagid` int(11) DEFAULT NULL COMMENT 'remove when finished',
  PRIMARY KEY (`id`),
  KEY `FK_categorylanguage` (`language_id`),
  CONSTRAINT `products_categories_language_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_categories_language
-- ----------------------------

-- ----------------------------
-- Table structure for `products_information`
-- ----------------------------
DROP TABLE IF EXISTS `products_information`;
CREATE TABLE `products_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `information_group_id` int(11) unsigned DEFAULT NULL,
  `language_id` int(11) unsigned DEFAULT '1',
  `product_category_id` int(11) unsigned DEFAULT NULL,
  `sort_id` int(11) unsigned DEFAULT NULL,
  `new` tinyint(4) DEFAULT '0',
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description_short` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `url_slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_informationgroupid_language` (`information_group_id`,`language_id`),
  KEY `FK_productlanguage` (`language_id`),
  KEY `FK_productcategory` (`product_category_id`),
  FULLTEXT KEY `ProductSearchHelpertitle` (`title`,`description`),
  CONSTRAINT `products_information_ibfk_1` FOREIGN KEY (`product_category_id`) REFERENCES `products_categories_language` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `products_information_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_information
-- ----------------------------

-- ----------------------------
-- Table structure for `products_price`
-- ----------------------------
DROP TABLE IF EXISTS `products_price`;
CREATE TABLE `products_price` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(11) unsigned DEFAULT NULL,
  `product_id` int(11) unsigned DEFAULT NULL,
  `price` decimal(6,2) DEFAULT '0.00',
  `price_purchase` decimal(6,2) DEFAULT NULL,
  `price_valuta_id` int(11) unsigned DEFAULT '1',
  `price_tax_id` int(11) unsigned DEFAULT '1',
  `admin` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pricecountry` (`country_id`),
  KEY `FK_priceproduct` (`product_id`),
  CONSTRAINT `products_price_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  CONSTRAINT `products_price_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_price
-- ----------------------------

-- ----------------------------
-- Table structure for `products_price_tax`
-- ----------------------------
DROP TABLE IF EXISTS `products_price_tax`;
CREATE TABLE `products_price_tax` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tax` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_price_tax
-- ----------------------------

-- ----------------------------
-- Table structure for `products_to_products_information`
-- ----------------------------
DROP TABLE IF EXISTS `products_to_products_information`;
CREATE TABLE `products_to_products_information` (
  `product_id` int(11) unsigned NOT NULL,
  `product_information_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`,`product_information_id`),
  KEY `FK_prdtoprdinfo_infoid` (`product_information_id`),
  CONSTRAINT `products_to_products_information_ibfk_1` FOREIGN KEY (`product_information_id`) REFERENCES `products_information` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `products_to_products_information_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_to_products_information
-- ----------------------------

-- ----------------------------
-- Table structure for `products_volume_units`
-- ----------------------------
DROP TABLE IF EXISTS `products_volume_units`;
CREATE TABLE `products_volume_units` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `volume_unit` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_volume_units
-- ----------------------------

-- ----------------------------
-- Table structure for `products_volume_units_language`
-- ----------------------------
DROP TABLE IF EXISTS `products_volume_units_language`;
CREATE TABLE `products_volume_units_language` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `volume_units_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned DEFAULT NULL,
  `volume_unit` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `volume_unit_long` varchar(70) COLLATE utf8_unicode_ci DEFAULT NULL,
  `multiple_from_base` int(11) DEFAULT '0' COMMENT 'base,ones,units, "fingers" ; base =1 kilo = 1000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_volunit` (`volume_units_id`),
  KEY `FK_volunitlang` (`language_id`),
  CONSTRAINT `products_volume_units_language_ibfk_1` FOREIGN KEY (`volume_units_id`) REFERENCES `products_volume_units` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `products_volume_units_language_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of products_volume_units_language
-- ----------------------------
DELIMITER ;;
CREATE TRIGGER `before_insert_products` BEFORE INSERT ON `products` FOR EACH ROW BEGIN
    SET new.uuid = uuid();
END
;;
DELIMITER ;
