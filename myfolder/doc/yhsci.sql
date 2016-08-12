/*
Navicat MySQL Data Transfer

Source Server         : 192.168.5.201
Source Server Version : 50512
Source Host           : 192.168.5.201:3306
Source Database       : hezq_test

Target Server Type    : MYSQL
Target Server Version : 50512
File Encoding         : 65001

Date: 2016-03-29 10:06:21
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `info_user`
-- ----------------------------
DROP TABLE IF EXISTS `info_user`;
CREATE TABLE `info_user` (
  `userPhone` varchar(20) NOT NULL DEFAULT '',
  `cookieid` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `ownb` int(11) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `career` varchar(500) DEFAULT NULL,
  `money` varchar(20) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `memo` varchar(1000) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`userPhone`),
  UNIQUE KEY `userPhone` (`userPhone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of info_user
-- ----------------------------
INSERT INTO `info_user` VALUES ('13660643919', '54f3b868489e3', '李木易', '307182358', '-1', '广东省广州市花都区学府路1号华南理工大学广州学院', '大学老师', '', '广东', '深圳', '', '2015-03-02 09:12:30');
INSERT INTO `info_user` VALUES ('13762090630', '54f3b9cb3d52c', '李红霞', '149422065', '1', '湖南省岳阳市岳阳楼区三眼桥派出所', '岳阳楼公安分局', '', '湖南', '岳阳', '', '2015-03-02 09:18:25');
INSERT INTO `info_user` VALUES ('13790755624', '54f3ba8d226ae', '龚宇', '1132782898', '1', '广东省惠州市博罗县罗浮山75210部队73分队', '你懂的', '500', '广东', '惠州', '多多联系，互相帮助，共同进步。祝各位事业有成，家庭幸福。', '2015-03-05 13:04:17');
INSERT INTO `info_user` VALUES ('15101019215', '54f325df4f215', '何钟强', '409325244', '1', '北京', '软件产品', '1000', '北京', '东城', '没有了', '2015-03-01 22:46:24');
INSERT INTO `info_user` VALUES ('15200520058', '54f462d36d9f2', '易国良', '1029480048', '-1', '1029480048@ .com', '医生', '500', '湖南', '衡阳', '', '2015-03-02 21:23:09');
INSERT INTO `info_user` VALUES ('15211148262', '5500fb55e80aa', '刘芳', '406486812', '1', '湖南省长沙市芙蓉中路二段279号金源大酒店北楼17层', '', '1000', '湖南', '长沙', '', '2015-03-12 10:37:57');
INSERT INTO `info_user` VALUES ('18075717709', '54f3b4251869e', '罗媛', '382674771', '1', '湖南岳阳步行街北辅道君临新世界C区321室', '中医减重塑形；瑜伽，肚皮舞文化游学。', '500', '湖南', '长沙', '希望和大家一起坚持做点有意义的事。', '2015-03-02 09:00:36');
INSERT INTO `info_user` VALUES ('18665418418', '54f33bf8bd902', '谭奔', '389443021', '1', '佛山市顺德区北滘镇美的大道美的总部大楼d座9楼审计法务部', '公司律师', '1000', '广东', '佛山', '希望延续下去', '2015-03-02 00:33:36');
INSERT INTO `info_user` VALUES ('18676565021', '54f39ee643898', '彭满龙', '389667624', '1', '广东省佛山市南海区益禾人才公寓', '汽车工程师', '500', '广东', '佛山', '', '2015-03-02 07:24:03');

-- ----------------------------
-- Table structure for `regist_user`
-- ----------------------------
DROP TABLE IF EXISTS `regist_user`;
CREATE TABLE `regist_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `userPhone` varchar(20) DEFAULT NULL,
  `chs` int(11) DEFAULT NULL,
  `dt` varchar(20) DEFAULT NULL,
  `memo` varchar(1000) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of regist_user
-- ----------------------------
INSERT INTO `regist_user` VALUES ('1', '何钟强', '15101019215', '1', '-1', '喝咯哦', '2015-01-28 00:52:59');
INSERT INTO `regist_user` VALUES ('2', '吴春兰', '15073015545', '1', '0216', '可能要去乡下过年，时间不定，尽量到', '2015-01-28 09:39:55');
INSERT INTO `regist_user` VALUES ('3', '吴春兰', '15073015545', '1', '0216', '可能要去乡下过年，时间不定，尽量到', '2015-01-28 09:40:07');
INSERT INTO `regist_user` VALUES ('4', '孔贝', '13975073036', '1', '2', '搞得热闹点，不要拼酒。', '2015-01-28 09:50:28');
INSERT INTO `regist_user` VALUES ('5', '谢祖敏', '18665924406', '1', '0216', '', '2015-01-28 10:15:38');
INSERT INTO `regist_user` VALUES ('6', '李红霞', '13762090630', '1', '0216', '尽量去', '2015-01-28 10:18:36');
INSERT INTO `regist_user` VALUES ('7', '李红霞', '13762090630', '1', '0216', '尽量去', '2015-01-28 10:18:40');
INSERT INTO `regist_user` VALUES ('8', '文学', '15201984805', '1', '0216', '', '2015-01-28 10:32:56');
INSERT INTO `regist_user` VALUES ('9', '测试', '15101019215', '1', '2', '', '2015-01-28 10:39:57');
INSERT INTO `regist_user` VALUES ('10', '彭满龙', '18676565021', '1', '0216', '准备初四出发去浙江，年前吧。希望大家都能到就好咯', '2015-01-28 11:00:11');
INSERT INTO `regist_user` VALUES ('11', '罗媛', '18075717709', '1', '2', '近期都在岳阳，可以做志愿者。', '2015-01-28 11:54:39');
INSERT INTO `regist_user` VALUES ('12', '谭奔', '18665418418', '1', '0216', '', '2015-01-28 13:40:32');
INSERT INTO `regist_user` VALUES ('13', '谭奔', '18665418418', '1', '0216', '', '2015-01-28 13:40:34');
INSERT INTO `regist_user` VALUES ('14', '谭奔', '18665418418', '1', '0216', '', '2015-01-28 13:40:40');
INSERT INTO `regist_user` VALUES ('15', '李木易', '13660643919', '1', '0216', '', '2015-01-28 14:07:46');
INSERT INTO `regist_user` VALUES ('16', '李响', '18325077890', '1', '0216', '尽量参加，但不知到时能否到场，主要是离岳阳远了点', '2015-01-28 14:08:27');
INSERT INTO `regist_user` VALUES ('17', '李响', '18325077890', '1', '0216', '尽量参加，但不知到时能否到场，主要是离岳阳远了点', '2015-01-28 14:08:33');
INSERT INTO `regist_user` VALUES ('18', '李木易', '13660643919', '1', '2', '', '2015-01-28 14:35:04');
INSERT INTO `regist_user` VALUES ('19', '徐飞祥', '15902706367', '-1', '', '宝宝年底要出生。', '2015-01-28 15:21:20');
INSERT INTO `regist_user` VALUES ('20', '朱江', '18086109176', '-1', '-1', '我30才放假，初二就要去老婆家挨个走亲戚。估计是没法参加了。带我向各位同学，老师问好', '2015-01-29 00:13:53');
INSERT INTO `regist_user` VALUES ('21', '粟海斌', '18506618500', '1', '0216', '', '2015-01-29 00:36:31');
INSERT INTO `regist_user` VALUES ('22', '何钟强', '15101019215', '1', '0222', '', '2015-01-29 12:23:37');
INSERT INTO `regist_user` VALUES ('23', '李杰', '18773037667', '1', '-1', '腊月二十五六有时间', '2015-01-30 10:00:11');
INSERT INTO `regist_user` VALUES ('24', '陈溦', '18873098883', '1', '0222', '', '2015-01-30 21:49:51');

-- ----------------------------
-- Table structure for `yh_wed`
-- ----------------------------
DROP TABLE IF EXISTS `yh_wed`;
CREATE TABLE `yh_wed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) DEFAULT '1' COMMENT '1:默认，2：专属',
  `cname` varchar(50) DEFAULT NULL,
  `cphone` varchar(20) DEFAULT NULL,
  `cwish` varchar(1000) DEFAULT NULL,
  `ctype` int(11) DEFAULT '1' COMMENT '1不来，2来',
  `ccount` int(11) DEFAULT NULL COMMENT '几个人',
  `ccookieid` varchar(50) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `last_update_time` datetime DEFAULT NULL,
  `status` int(11) DEFAULT '1' COMMENT '1:刚提交 2:审核通过',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yh_wed
-- ----------------------------
INSERT INTO `yh_wed` VALUES ('3', '1', '何钟强', '15101019215', '就这样吧呢了啊', '1', '1', '0QoD0lXuX7', '2016-01-20 08:08:07', '2016-01-21 07:58:36', '1');
INSERT INTO `yh_wed` VALUES ('4', '1', '谭晓红', '13874042478', '祝你们爱情甜蜜，幸福美满。', '1', '1', 'GAOnH2skfi', '2016-01-20 19:25:09', '2016-01-20 19:25:09', '1');
INSERT INTO `yh_wed` VALUES ('5', '1', '何燕', '15773031123', '祝侄儿', '1', '1', 'svAYvD8iG6', '2016-01-20 20:45:00', '2016-01-20 20:45:00', '1');
INSERT INTO `yh_wed` VALUES ('6', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:48:53', '2016-01-20 20:48:53', '1');
INSERT INTO `yh_wed` VALUES ('7', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:48:54', '2016-01-20 20:48:54', '1');
INSERT INTO `yh_wed` VALUES ('8', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:48:55', '2016-01-20 20:48:55', '1');
INSERT INTO `yh_wed` VALUES ('9', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:49:27', '2016-01-20 20:49:27', '1');
INSERT INTO `yh_wed` VALUES ('10', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:49:28', '2016-01-20 20:49:28', '1');
INSERT INTO `yh_wed` VALUES ('11', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:49:29', '2016-01-20 20:49:29', '1');
INSERT INTO `yh_wed` VALUES ('12', '1', '姑妈何燕', '15773031123', '祝侄儿新婚快乐！幸福长长久久满满', '1', '1', 'svAYvD8iG6', '2016-01-20 20:49:43', '2016-01-20 20:49:43', '1');
INSERT INTO `yh_wed` VALUES ('13', '1', '毛贞', '13975056466', '新婚快乐！', '1', '1', 'qqViEDALDz', '2016-01-21 09:54:56', '2016-01-21 09:54:56', '1');
INSERT INTO `yh_wed` VALUES ('14', '1', 'Doris', '18500228770', '怀念帮我加班的日子，离开后见面少了，也没关心你的近况。还好你顺利娶上了想要的媳妇儿', '1', '1', 'NQ5e4kPOjn', '2016-01-21 10:15:02', '2016-01-21 10:15:02', '1');
INSERT INTO `yh_wed` VALUES ('15', '1', '张晓燕', '15001083593', '何老板，恭喜，新婚快乐，幸福永远', '1', '1', 'RXRFysTai1', '2016-01-21 10:38:42', '2016-01-21 10:38:42', '1');
INSERT INTO `yh_wed` VALUES ('16', '1', '思思，沈浩', '18073008089', '新婚快乐！家庭美满', '1', '1', 'EYFvhSIlSM', '2016-01-21 14:09:57', '2016-01-21 14:09:57', '1');
INSERT INTO `yh_wed` VALUES ('17', '1', '思思，沈浩', '18073008089', '新婚快乐！家庭美满', '1', '1', 'EYFvhSIlSM', '2016-01-21 14:10:00', '2016-01-21 14:10:00', '1');
INSERT INTO `yh_wed` VALUES ('18', '1', '李巧灵', '15815537468', '新婚快乐，早生贵子！', '1', '1', 'kDGwMhrCe5', '2016-01-23 16:00:20', '2016-01-23 16:00:20', '1');
INSERT INTO `yh_wed` VALUES ('19', '1', '陌生人', '15697617766', '祝福你们，愿你们可以一直浪漫幸福下去', '1', '1', 'dL5BnINm4e', '2016-01-23 16:20:43', '2016-01-23 16:20:43', '1');
INSERT INTO `yh_wed` VALUES ('20', '1', '陌生人', '15697617766', '祝福你们，愿你们可以一直浪漫幸福下去', '1', '1', 'dL5BnINm4e', '2016-01-23 16:20:45', '2016-01-23 16:20:45', '1');
INSERT INTO `yh_wed` VALUES ('21', '2', '杨华', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-01-27 16:15:52', '2016-01-27 16:15:52', '1');
INSERT INTO `yh_wed` VALUES ('22', '2', '杨亮', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-01-27 17:38:25', '2016-01-27 17:38:25', '1');
INSERT INTO `yh_wed` VALUES ('23', '1', '杨娟', '13800138000', '恭喜恭喜', '1', '1', 'xQdu9ro3N7', '2016-01-27 20:37:59', '2016-01-27 20:42:34', '1');
INSERT INTO `yh_wed` VALUES ('24', '1', '杨娟', '13800138000', '恭喜恭喜', '1', '1', 'xQdu9ro3N7', '2016-01-27 20:37:59', '2016-01-27 20:42:34', '1');
INSERT INTO `yh_wed` VALUES ('25', '2', '郭啸', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:08:56', '2016-02-04 20:08:56', '1');
INSERT INTO `yh_wed` VALUES ('26', '2', '徐妮', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:09:12', '2016-02-04 20:09:12', '1');
INSERT INTO `yh_wed` VALUES ('27', '2', '朱江', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:09:21', '2016-02-04 20:09:21', '1');
INSERT INTO `yh_wed` VALUES ('28', '2', '沈浩&amp;陈思思', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:10:04', '2016-02-04 20:10:04', '1');
INSERT INTO `yh_wed` VALUES ('29', '2', '孔贝&amp;毛贞', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:10:43', '2016-02-04 20:10:43', '1');
INSERT INTO `yh_wed` VALUES ('30', '2', '李微', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:11:43', '2016-02-04 20:11:43', '1');
INSERT INTO `yh_wed` VALUES ('31', '2', '童芳', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:12:10', '2016-02-04 20:12:10', '1');
INSERT INTO `yh_wed` VALUES ('32', '2', '李灿', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:12:46', '2016-02-04 20:12:46', '1');
INSERT INTO `yh_wed` VALUES ('33', '2', '李华', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:12:55', '2016-02-04 20:12:55', '1');
INSERT INTO `yh_wed` VALUES ('34', '2', '谭艳鸿', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:13:33', '2016-02-04 20:13:33', '1');
INSERT INTO `yh_wed` VALUES ('35', '2', '李鹏', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:15:56', '2016-02-04 20:15:56', '1');
INSERT INTO `yh_wed` VALUES ('36', '2', '彭满龙', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:20:53', '2016-02-04 20:20:53', '1');
INSERT INTO `yh_wed` VALUES ('37', '2', '李桦', '13800138000', '恭喜恭喜', '1', '1', '0QoD0lXuX7', '2016-02-04 20:25:21', '2016-02-04 20:25:21', '1');
INSERT INTO `yh_wed` VALUES ('38', '2', '叶加老师', '13800138000', '恭喜恭喜', '1', '1', 'xQdu9ro3N7', '2016-02-08 19:21:29', '2016-02-08 19:21:29', '1');
INSERT INTO `yh_wed` VALUES ('39', '2', '张飞', '13800138000', '恭喜恭喜', '1', '1', 'xQdu9ro3N7', '2016-02-08 19:25:36', '2016-02-08 19:25:36', '1');
INSERT INTO `yh_wed` VALUES ('40', '1', '王丁', '13051581702', '师姐幸福呦～', '1', '1', '7Si7yoZD8d', '2016-02-09 13:44:46', '2016-02-09 13:44:46', '1');
