/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50525
Source Host           : localhost:3306
Source Database       : points

Target Server Type    : MYSQL
Target Server Version : 50525
File Encoding         : 65001

Date: 2020-06-15 21:52:47
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `objects`
-- ----------------------------
DROP TABLE IF EXISTS `objects`;
CREATE TABLE `objects` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `point` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of objects
-- ----------------------------
INSERT INTO `objects` VALUES ('1', 'Тверская 9', '55.75985606898725,37.61054750000002');
INSERT INTO `objects` VALUES ('2', 'Тверская, 20', '55.766642568974845,37.60237299999997');
INSERT INTO `objects` VALUES ('3', 'Охотный Ряд, 1 ', '55.75805306898262,37.6160005');
INSERT INTO `objects` VALUES ('4', 'Солянка, 16 ', '55.75061056899327,37.64180899999995');
INSERT INTO `objects` VALUES ('5', 'Кривоколенный, 5', '56.739470,38.859725');
INSERT INTO `objects` VALUES ('6', 'Свободы, 22', '56.738794,38.862783');
INSERT INTO `objects` VALUES ('7', 'Свободы, 20', '56.738811,38.862418');
