/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50150
Source Host           : localhost:3306
Source Database       : yhsci

Target Server Type    : MYSQL
Target Server Version : 50150
File Encoding         : 65001

Date: 2016-03-30 15:42:31
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `wx_custom_menu`
-- ----------------------------
DROP TABLE IF EXISTS `wx_custom_menu`;
CREATE TABLE `wx_custom_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `name` varchar(50) NOT NULL COMMENT '菜单名称 ',
  `order` smallint(2) NOT NULL DEFAULT '100' COMMENT '单菜排序标志',
  `parent` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单id，0表示一级菜单',
  `type` smallint(1) NOT NULL DEFAULT '0' COMMENT '单菜类型,1：静态菜单,2:动态菜单,3:访问网页',
  `msg_type` varchar(10) NOT NULL DEFAULT '' COMMENT '消息类型；text/news/music/image/voice/video',
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `content` longtext COMMENT '文本消息内容',
  `url` varchar(255) DEFAULT NULL COMMENT '当type为2时 为动态回复地址 当type为3时 菜单转向url地址',
  `use_oauth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '监测数据是否开启oauth验证',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建日期',
  `last_update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '后最更新日期',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COMMENT='自定义菜单';

-- ----------------------------
-- Records of wx_custom_menu
-- ----------------------------
INSERT INTO wx_custom_menu VALUES ('17', 'menu01', '1', '0', '1', 'text', '-1', '这是菜单1', null, '0', '2016-03-29 17:46:25', '2016-03-29 17:46:25');
INSERT INTO wx_custom_menu VALUES ('18', '菜单二', '2', '0', '1', 'text', '-1', '澧', null, '0', '2016-03-29 17:48:50', '2016-03-29 17:48:50');
INSERT INTO wx_custom_menu VALUES ('19', '外链', '1', '17', '3', '', '0', null, 'http://www.baidu.com', '0', '2016-03-29 17:49:13', '2016-03-29 17:49:13');
INSERT INTO wx_custom_menu VALUES ('20', '直接返回', '3', '17', '1', 'text', '-1', '测试直接返回', null, '0', '2016-03-30 15:29:06', '2016-03-30 15:29:06');
INSERT INTO wx_custom_menu VALUES ('21', '测试素材', '23', '18', '1', 'news', '7', null, null, '0', '2016-03-30 15:29:35', '2016-03-30 15:29:35');

-- ----------------------------
-- Table structure for `wx_ent_setting`
-- ----------------------------
DROP TABLE IF EXISTS `wx_ent_setting`;
CREATE TABLE `wx_ent_setting` (
  `set_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `ent_id` int(11) NOT NULL DEFAULT '0' COMMENT '企业ID',
  `set_key` varchar(255) DEFAULT NULL COMMENT '企业设置项key',
  `set_value` longtext COMMENT '企业设置项KEY对应的值',
  PRIMARY KEY (`set_id`),
  UNIQUE KEY `index_ent_id_setkey` (`ent_id`,`set_key`),
  KEY `企业设置项key` (`set_key`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='企业设置表';

-- ----------------------------
-- Records of wx_ent_setting
-- ----------------------------
INSERT INTO wx_ent_setting VALUES ('1', '1', 'custom_menu_last_synchronous_time', '2016-03-30 15:30:18');
INSERT INTO wx_ent_setting VALUES ('2', '1', 'custom_menu_last_update_time', '2016-03-30 15:29:35');

-- ----------------------------
-- Table structure for `wx_material`
-- ----------------------------
DROP TABLE IF EXISTS `wx_material`;
CREATE TABLE `wx_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) DEFAULT '' COMMENT '素材标题，便于搜索',
  `description` varchar(255) DEFAULT NULL COMMENT '描述',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '素材类型：news/music/image/voice/video/template',
  `articles` longtext COMMENT 'news/music类型素材序列化的内容',
  `media_url` varchar(255) DEFAULT '' COMMENT 'image/voice/video素材的url地址',
  `template_id` varchar(64) DEFAULT NULL COMMENT '模版素材ID',
  `data` text COMMENT '模版素材格式内容',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='素材库';

-- ----------------------------
-- Records of wx_material
-- ----------------------------
INSERT INTO wx_material VALUES ('7', '', null, 'news', 'a:1:{i:0;a:4:{s:5:\"title\";s:4:\"imya\";s:11:\"description\";s:12:\"jsiadofjwetq\";s:6:\"picurl\";s:72:\"http://wx.hysci.com.cn/yhsci/Common/weixin/upload/201603301528077754.jpg\";s:3:\"url\";s:20:\"http://www.baidu.com\";}}', '', null, null, '2016-03-30 15:28:32');

-- ----------------------------
-- Table structure for `wx_material_news`
-- ----------------------------
DROP TABLE IF EXISTS `wx_material_news`;
CREATE TABLE `wx_material_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `news_index` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '图文消息顺序位置',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '图标标题',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '图文描述',
  `picurl` varchar(255) NOT NULL DEFAULT '' COMMENT '图文图片地址',
  `url` varchar(255) DEFAULT NULL COMMENT '原文URL',
  `news_text` longtext COMMENT '正文内容',
  `show_hdimg` int(11) DEFAULT '1' COMMENT '0:不显示头图 1：显示头图',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除：1是；0否',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='素材图文关系表';

-- ----------------------------
-- Records of wx_material_news
-- ----------------------------
INSERT INTO wx_material_news VALUES ('27', '7', '1', 'imya', 'jsiadofjwetq', 'http://wx.hysci.com.cn/yhsci/Common/weixin/upload/201603301528077754.jpg', 'http://www.baidu.com', '<p>asdfqkwjrtoqowut4oqjeajsdljfowq4qqt</p>', '1', '0', '2016-03-30 15:28:32');

-- ----------------------------
-- Table structure for `wx_material_news_preview`
-- ----------------------------
DROP TABLE IF EXISTS `wx_material_news_preview`;
CREATE TABLE `wx_material_news_preview` (
  `material_id` varchar(64) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `news_index` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '图文消息顺序位置',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '图标标题',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '图文描述',
  `picurl` varchar(255) NOT NULL DEFAULT '' COMMENT '图文图片地址',
  `url` varchar(255) DEFAULT NULL COMMENT '原文URL',
  `news_text` longtext COMMENT '正文内容',
  `show_hdimg` int(11) DEFAULT '1' COMMENT '0：不显示头图 1：显示头图',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除：1是；0否',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='素材图文关系表';

-- ----------------------------
-- Records of wx_material_news_preview
-- ----------------------------

-- ----------------------------
-- Table structure for `wx_message`
-- ----------------------------
DROP TABLE IF EXISTS `wx_message`;
CREATE TABLE `wx_message` (
  `msg_id` varchar(64) NOT NULL DEFAULT '' COMMENT '微信消息ID',
  `openid` varchar(128) NOT NULL DEFAULT '' COMMENT '来源微信用户',
  `msg_type` varchar(10) NOT NULL DEFAULT '' COMMENT '消息类型:目前支持五类text/image/voice/video/link',
  `content` longtext COMMENT '消息内容',
  `media_id` varchar(128) DEFAULT NULL COMMENT '图片ID；当消息为image/voice/video时，相应的ID',
  `media_url` varchar(255) DEFAULT NULL COMMENT '媒体url',
  `format` varchar(10) DEFAULT NULL COMMENT '语音格式，如amr，speex等',
  `recognition` varchar(255) DEFAULT NULL COMMENT '语音识别结果，UTF8编码',
  `thumb_media_id` varchar(128) DEFAULT NULL COMMENT '当类型为video/link,缩略图ID',
  `location_x` varchar(13) DEFAULT NULL COMMENT '地图纬度',
  `location_y` varchar(13) DEFAULT NULL COMMENT '地图经度',
  `scale` tinyint(3) DEFAULT NULL COMMENT '地图缩略数',
  `label` varchar(255) DEFAULT NULL COMMENT '地图上的地里位置',
  `title` varchar(255) DEFAULT NULL COMMENT '类型为link，消息标题',
  `description` varchar(255) DEFAULT NULL COMMENT '类型为link,消息描述',
  `url` varchar(255) DEFAULT NULL COMMENT '类型为link,消息链接',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消息状态：-1：原始消息；0：默认插件未命中需要分配到坐席；1是被插件自动回复；',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '消息发送的时间',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '消息采集时间',
  PRIMARY KEY (`msg_id`),
  KEY `index_status` (`status`),
  KEY `index_openid_state` (`openid`,`status`),
  KEY `index_openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信消息表';

-- ----------------------------
-- Records of wx_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wx_message_event`
-- ----------------------------
DROP TABLE IF EXISTS `wx_message_event`;
CREATE TABLE `wx_message_event` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息ID',
  `openid` varchar(128) NOT NULL DEFAULT '' COMMENT '消息来源用户ID',
  `event_type` varchar(30) NOT NULL DEFAULT '' COMMENT '事件类型subscribe(订阅)、unsubscribe(取消订阅)、click(自定义菜单点击事件)、location(地理位置)、templatesendjobfinish(模板消息回执事件)、masssendjobfinish(群发消息回执)',
  `event_key` varchar(255) NOT NULL DEFAULT '' COMMENT '事件KEY值，与自定义菜单接口中KEY值对应',
  `latitude` varchar(13) DEFAULT NULL COMMENT '地理位置纬度，事件类型为location的时存在',
  `longitude` varchar(13) DEFAULT NULL COMMENT '地理位置经度，事件类型为location的时存在',
  `precision` varchar(13) DEFAULT NULL COMMENT '地理位置精度，事件类型为LOCATION的时存在',
  `msg_id` varchar(64) DEFAULT NULL COMMENT '消息ID',
  `status` varchar(125) DEFAULT NULL COMMENT '模板消息发送回执状态',
  `total_count` int(11) NOT NULL DEFAULT '0' COMMENT '群发粉丝总数，事件类型为masssendjobfinish时存在',
  `filter_count` int(11) NOT NULL DEFAULT '0' COMMENT '过滤后群发粉丝总数，事件类型为masssendjobfinish时存在 过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数，原则上，FilterCount = SentCount + ErrorCount',
  `sent_count` int(11) NOT NULL DEFAULT '0' COMMENT '群发发送成功的粉丝数，事件类型为masssendjobfinish时存在',
  `error_count` int(11) NOT NULL DEFAULT '0' COMMENT '群发失败的粉丝数，事件类型为masssendjobfinish时存在',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '微信消息时间',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '消息创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5975 DEFAULT CHARSET=utf8 COMMENT='事件类型消息表';

-- ----------------------------
-- Records of wx_message_event
-- ----------------------------

-- ----------------------------
-- Table structure for `wx_user`
-- ----------------------------
DROP TABLE IF EXISTS `wx_user`;
CREATE TABLE `wx_user` (
  `openid` varchar(128) NOT NULL DEFAULT '' COMMENT '微信用户ID',
  `nickname` varchar(128) NOT NULL DEFAULT '' COMMENT '微信用户昵称',
  `remark` varchar(128) NOT NULL DEFAULT '' COMMENT '信微用户备注',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '性别：0未知；1男；2女',
  `country` varchar(64) DEFAULT NULL COMMENT '国家',
  `province` varchar(32) DEFAULT NULL COMMENT '微信用户省',
  `city` varchar(32) DEFAULT NULL COMMENT '微信用户市',
  `language` varchar(32) DEFAULT NULL COMMENT '微信用户语言',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空',
  `subscribe_time` int(11) NOT NULL DEFAULT '0' COMMENT '用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间',
  `subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是企业微信订阅用户：1是；0否',
  `ent_subscribe` tinyint(1) NOT NULL DEFAULT '0' COMMENT '企业订阅：1是；0否',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '信息创建时间',
  `last_update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后更新时间',
  PRIMARY KEY (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信用户表';

-- ----------------------------
-- Records of wx_user
-- ----------------------------

-- ----------------------------
-- Table structure for `wx_welcome`
-- ----------------------------
DROP TABLE IF EXISTS `wx_welcome`;
CREATE TABLE `wx_welcome` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `type` smallint(1) NOT NULL DEFAULT '1' COMMENT '欢迎词类型  1：系统设置 2：动态获取',
  `msg_type` varchar(10) NOT NULL DEFAULT '' COMMENT '系统设置数据类型 text/news/music/image/voice/video',
  `content` text COMMENT '文本内容',
  `url` varchar(255) DEFAULT '' COMMENT '当type为2时 为动态回复地址',
  `material_id` int(11) NOT NULL DEFAULT '0' COMMENT '素材ID',
  `use_oauth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '图文监测数据微信用户是否oauth验证',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wx_welcome
-- ----------------------------

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yh_wed
-- ----------------------------
INSERT INTO yh_wed VALUES ('3', '1', 'addd', '13800138000', 'asdfasdf', '1', '1', '5iF1Qoua8W', '2016-01-27 15:55:31', '2016-01-27 15:55:31', '1');
INSERT INTO yh_wed VALUES ('4', '1', 'adddeee', '13800138000', 'asdfasdf', '1', '1', '5iF1Qoua8W', '2016-01-27 15:55:37', '2016-01-27 15:55:37', '1');
INSERT INTO yh_wed VALUES ('5', '2', '杨亮', '13800138000', '恭喜恭喜', '1', '1', '5iF1Qoua8W', '2016-01-27 16:12:56', '2016-01-27 16:12:56', '1');
