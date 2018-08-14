/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50719
Source Host           : 127.0.0.1:3306
Source Database       : kbctm3

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2018-04-25 09:40:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ykb_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `ykb_admin_user`;
CREATE TABLE `ykb_admin_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL DEFAULT '' COMMENT '用户名，登陆使用',
  `nickname` varchar(15) NOT NULL DEFAULT '' COMMENT '管理员昵称',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '电子邮箱，登陆使用',
  `phone` char(11) NOT NULL DEFAULT '0' COMMENT '手机号码，登陆使用',
  `password` varchar(32) NOT NULL DEFAULT 'e10adc3949ba59abbe56e057f20f883e' COMMENT '用户密码',
  `thumb` int(10) NOT NULL DEFAULT '0' COMMENT '管理员头像',
  `login_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '登陆状态,0:pc,1:app',
  `login_code` varchar(32) NOT NULL DEFAULT '0' COMMENT '排他性登陆标识,token',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用，0禁用',
  `is_delete` tinyint(1) NOT NULL DEFAULT '1' COMMENT '删除状态:1正常,0已删除',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `salt` char(6) NOT NULL DEFAULT '0' COMMENT '密码加盐处理',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`,`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_admin_user
-- ----------------------------
INSERT INTO `ykb_admin_user` VALUES ('1', 'admin', '壹凯', '417170808@qq.com', '18580008000', '4d8fa923ad18963c9ede56bb7c0de115', '5', '0', '0', '0.0.0.0', '1524620208', '1', '0', '1520314868', '4MzHJo');
INSERT INTO `ykb_admin_user` VALUES ('2', 'test', 'ceshi', 'ceshi@andphp.com', '18888888888', 'eab744fdded32ed55bcf192bc6bbfb57', '2', '0', '0', '0.0.0.0', '1521600104', '1', '0', '1520326035', 'Nc1l95');
INSERT INTO `ykb_admin_user` VALUES ('3', 'ceshi', 'ceshi_9NrrLF', 'ceshi@andphp.cn', '18854554564', 'a3fe48588e0c18592460a8e08135831b', '2', '0', '0', '0', '0', '1', '1', '1520326830', '9NrrLF');

-- ----------------------------
-- Table structure for ykb_attachment
-- ----------------------------
DROP TABLE IF EXISTS `ykb_attachment`;
CREATE TABLE `ykb_attachment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` char(15) NOT NULL DEFAULT '' COMMENT '所属模块',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` varchar(255) NOT NULL DEFAULT '' COMMENT '保存文件名',
  `savepath` varchar(255) NOT NULL DEFAULT '' COMMENT '保存文件路径',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `ext` char(6) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件mime类型',
  `resolution` varchar(11) NOT NULL DEFAULT '' COMMENT '分辨率',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件md5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  `location` int(1) NOT NULL DEFAULT '0' COMMENT '储存位置:0本地，1七牛云',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `uploadip` char(15) NOT NULL DEFAULT '' COMMENT '上传IP',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未审核1已审核7不通过',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `admin_id` int(11) NOT NULL COMMENT '审核者id',
  `audit_time` int(11) NOT NULL COMMENT '审核时间',
  `use` varchar(200) DEFAULT '' COMMENT '用处',
  `download` int(11) NOT NULL DEFAULT '0' COMMENT '下载量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='附件表';

-- ----------------------------
-- Records of ykb_attachment
-- ----------------------------
INSERT INTO `ykb_attachment` VALUES ('1', 'admin', '{8319C9C1-378F-7A9C-C51A-C1266F29CFD4}.png', 'ecbd2f8dd06090b2336645d3b9f7720c.png', '/uploads/admin/admin_thumb/20180306/ecbd2f8dd06090b2336645d3b9f7720c.png', '36129', 'png', 'image/png', '', 'f2ab7eb781b316fc4560ea5922dbb7a9', '2d8a650806887569363249edd28228a9b3b1799e', '0', '1', '127.0.0.1', '1', '1520314866', '1', '1520314866', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('2', 'admin', '0cb9769c7bac400a8adc6412c8cd2451.jpg', '2663da766cf2163b9f4254542ccc142e.jpg', '/uploads/admin/admin_thumb/20180306/2663da766cf2163b9f4254542ccc142e.jpg', '11481', 'jpg', 'image/jpeg', '', '9eb296aac50b30b688776f1b8070ba09', 'bccd8755de266dcb5139bbc96034bbda1f2be6de', '0', '1', '127.0.0.1', '1', '1520315030', '1', '1520315030', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('3', 'admin', '123.png', 'fea3fbba5f988961b3f2dbe6f99db1ba.png', '/uploads/admin/admin_thumb/20180306/fea3fbba5f988961b3f2dbe6f99db1ba.png', '27183', 'png', 'image/png', '', 'f49196becef9db1e6d320596eb66dada', '287e9757e4c3c77d3427c0de88a7d4324100e50f', '0', '1', '127.0.0.1', '1', '1520315810', '1', '1520315810', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('4', 'admin', 'u=713785851,1735763299&fm=27&gp=0.jpg', '434d483303135bd8403134a9530b6f40.jpg', '/uploads/admin/admin_thumb/20180321/434d483303135bd8403134a9530b6f40.jpg', '7604', 'jpg', 'image/jpeg', '', 'ff345851c21c3c3a65ac7f3c4e93f8e0', '5729ae7818fa82343759cf803205af22ce98554d', '0', '1', '0.0.0.0', '1', '1521618215', '1', '1521618215', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('5', 'admin', 'default.png', '968fa76bfedef39982001b235d5a765e.png', '/uploads/admin/admin_thumb/20180321/434d483303135bd8403134a9530b6f40.jpg', '6727', 'png', 'image/png', '', '1d39c0e3d42c39c7db4bd9e3202d4410', 'fa6c0a1b774d0db4d1ee251ddbd28d70b889daae', '0', '1', '0.0.0.0', '1', '1521770075', '1', '1521770075', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('6', 'admin', '9.jpg', '82612405a119d2567020a7010d86fcc8.jpg', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', '6804', 'jpg', 'image/jpeg', '', '424b3311712b36d3d7b9b1ff78dbeaa0', 'c4c99c421da8f96919bafe8183e5d3bbd5538690', '0', '1', '0.0.0.0', '1', '1521771263', '1', '1521771263', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('7', 'admin', '1.jpg', '50e883a6ae063b2aa78c52d39f0dbe02.jpg', '/uploads/admin/admin_thumb/20180323/50e883a6ae063b2aa78c52d39f0dbe02.jpg', '5504', 'jpg', 'image/jpeg', '', '8897aa57607c675190c6f0a536a45a45', '1a458a65f2a4fa66a6ea311e519da6648bfa974b', '0', '1', '0.0.0.0', '1', '1521771444', '1', '1521771444', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('8', 'admin', 'u=128308122,770382628&fm=27&gp=0.jpg', '5c6260511edc05646bb10497cd3cf37c.jpg', '/uploads/admin/admin_thumb/20180323/5c6260511edc05646bb10497cd3cf37c.jpg', '6076', 'jpg', 'image/jpeg', '', 'b9f8ab766636d8c5b4b6f01987619ebd', '6bb21f31a141de46d387e9eb7bf3e899210505b3', '0', '1', '0.0.0.0', '1', '1521772502', '1', '1521772502', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('9', 'admin', '10.jpg', '5e366cf03ef3362c18ad8054188a126a.jpg', '/uploads/admin/admin_thumb/20180323/5e366cf03ef3362c18ad8054188a126a.jpg', '6377', 'jpg', 'image/jpeg', '', '59e0b1a72431c8c3a694a58f6c8cef9a', 'ebfaefb20fa9d53408da8415874e4e9706ce3eb6', '0', '1', '0.0.0.0', '1', '1521772591', '1', '1521772591', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('10', 'admin', '11.jpg', 'b9005b6d1e0a6454b3adef21ba265df0.jpg', '/uploads/admin/admin_thumb/20180323/b9005b6d1e0a6454b3adef21ba265df0.jpg', '5754', 'jpg', 'image/jpeg', '', '8d6c631ae5af5de348d0560f48d34c84', '7e219ff171a6d87fedb0f16b51a526b00c316cad', '0', '1', '0.0.0.0', '1', '1521772741', '1', '1521772741', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('11', 'admin', '4.jpg', 'df164dcf3a9bd2e79d348a44ec8f9eb2.jpg', '/uploads/admin/admin_thumb/20180323/df164dcf3a9bd2e79d348a44ec8f9eb2.jpg', '6404', 'jpg', 'image/jpeg', '', '0a0f12ebf8c982803c6649173593037c', '2d246cac75220550154bf7701bffaf290bb98246', '0', '1', '0.0.0.0', '1', '1521772853', '1', '1521772853', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('12', 'admin', 'new_home.png', '28bd92da1955bf737fa7840139c8d5b1.png', '/uploads/admin/admin_thumb/20180323/28bd92da1955bf737fa7840139c8d5b1.png', '1449002', 'png', 'image/png', '', 'b94ddc54247fb311938551d9921963c2', 'c564ecfae6928c08dc2bf60c6a3843c28ccee611', '0', '1', '0.0.0.0', '1', '1521775289', '1', '1521775289', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('13', 'admin', '微信图片_20170915162827.png', '248570da8a7dd64e94503b701b98da59.png', '/uploads/admin/admin_thumb/20180323/248570da8a7dd64e94503b701b98da59.png', '1356536', 'png', 'image/png', '', '1eb43e119f95723c97da508c3b606936', 'b0506a239aa1ccf4e95a310414e8cac04100e8a0', '0', '1', '0.0.0.0', '1', '1521775445', '1', '1521775445', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('14', 'admin', '微信图片_20170915162819.png', '0c48732acbca4d52a4ea78e240f6a7f5.png', '/uploads/admin/admin_thumb/20180323/0c48732acbca4d52a4ea78e240f6a7f5.png', '402667', 'png', 'image/png', '', 'a3b612eead02148d881542733c31c9e8', 'fa35e37934b9a3de8e79eba503dd9d58435689d1', '0', '1', '0.0.0.0', '1', '1521775449', '1', '1521775449', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('15', 'admin', '9.jpg', '38e57a3f0ea5ba8008a8ae36d2be6968.jpg', '/uploads/admin/admin_thumb/20180323/38e57a3f0ea5ba8008a8ae36d2be6968.jpg', '11011', 'jpg', 'image/jpeg', '', 'c094bce011be14ede91a7d2bc88c6e1a', '1bc945a8b0ec179c664070200e9185200262ac31', '0', '1', '0.0.0.0', '1', '1521784116', '1', '1521784116', 'admin_thumb', '0');
INSERT INTO `ykb_attachment` VALUES ('19', 'admin', '8.jpg', 'b75f34c1adfcab7e25e550d0f7ae8d81.jpg', '/uploads/admin/admin/20180323/b75f34c1adfcab7e25e550d0f7ae8d81.jpg', '8891', 'jpg', 'image/jpeg', '', '175e997d8b6f4f57c0a012567316c4f5', '089eb9e4f7abaf905f9f9c420c37cc0717eb8f0f', '0', '1', '0.0.0.0', '1', '1521788570', '1', '1521788570', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('20', 'admin', 'u=219634610,112559926&fm=27&gp=0.jpg', 'eafbd6ba392405aeb55e21659c62e6c6.jpg', '/uploads/admin/admin/20180323/eafbd6ba392405aeb55e21659c62e6c6.jpg', '16734', 'jpg', 'image/jpeg', '', '85631e496289ccf20952058185bef555', 'c1ec33adbce5e582051a736dfcb7bf870816bfdb', '0', '1', '0.0.0.0', '1', '1521788607', '1', '1521788607', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('21', 'admin', 'u=277397705,3100574613&fm=27&gp=0.jpg', 'c6ebe6bee13040f8a0b7c77b902a6664.jpg', '/uploads/admin/admin/20180323/c6ebe6bee13040f8a0b7c77b902a6664.jpg', '6519', 'jpg', 'image/jpeg', '', 'd7b77938b1ffe9e2e8574cc33eb23eec', 'aa9be0ea65654fcd0c5b9f08a47bbb914828b0eb', '0', '1', '0.0.0.0', '1', '1521788607', '1', '1521788607', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('22', 'admin', 'u=219634610,112559926&fm=27&gp=0.jpg', '955343e46f6d6233f76cba6aefe6d130.jpg', '/uploads/admin/admin/20180326/955343e46f6d6233f76cba6aefe6d130.jpg', '9478', 'jpg', 'image/jpeg', '', '609f28aa2654a0cfa67b7256bf944282', 'e859db5e6d4bb3642d4ff0375f1210cec7238586', '0', '1', '0.0.0.0', '1', '1522034358', '1', '1522034358', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('23', 'admin', '0.jpg', '0ba99ad8a0651fbead354a15b265667f.jpg', '/uploads/admin/admin/20180326/0ba99ad8a0651fbead354a15b265667f.jpg', '4989', 'jpg', 'image/jpeg', '', 'b2311df796be898a697475e46e3b52de', '8d86226f082819de8bc48890e94a1708b1fa375b', '0', '1', '0.0.0.0', '1', '1522041864', '1', '1522041864', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('24', 'admin', '10.jpg', 'a45d6e47b2c8abefbff3accb4969d8d4.jpg', '/uploads/admin/admin/20180326/a45d6e47b2c8abefbff3accb4969d8d4.jpg', '8021', 'jpg', 'image/jpeg', '', '4adbf4e10f43305283a7a3977c93a51c', '25a32ffed3c060717813513f7372415e93343a77', '0', '1', '0.0.0.0', '1', '1522041870', '1', '1522041870', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('25', 'admin', '9.jpg', 'aa0303fb1a3d4eb5fd5e7b6a64789705.jpg', '/uploads/admin/admin/20180326/aa0303fb1a3d4eb5fd5e7b6a64789705.jpg', '7707', 'jpg', 'image/jpeg', '', 'fde9b900b3482714bb729f4945986d63', '2a07fbb16deb829af7cbfe04f08da735ef15d04d', '0', '1', '0.0.0.0', '1', '1522041870', '1', '1522041870', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('26', 'admin', '11.jpg', 'aba45bec19139b0b0b4a341c16867b6d.jpg', '/uploads/admin/admin/20180326/aba45bec19139b0b0b4a341c16867b6d.jpg', '7499', 'jpg', 'image/jpeg', '', '05d1cf383ab79d3fd89ab41bb8463e38', '690b1b2e054ba6a5dd33a5bfbc23c1906ef919f5', '0', '1', '0.0.0.0', '1', '1522041870', '1', '1522041870', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('27', 'admin', '10.jpg', '49bda7791fef836aae7161d6b92a24ca.jpg', '/uploads/admin/admin/20180326/49bda7791fef836aae7161d6b92a24ca.jpg', '11157', 'jpg', 'image/jpeg', '', 'dc2f681fc21ea8f186d4fe5d221763d9', 'b84a1710d83148ea3f6a04832ff36eae2f2f7b59', '0', '1', '0.0.0.0', '1', '1522043183', '1', '1522043183', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('28', 'admin', '8.jpg', 'f2f66e9ef68dbb08c212473359fcffa3.jpg', '/uploads/admin/admin/20180326/f2f66e9ef68dbb08c212473359fcffa3.jpg', '14488', 'jpg', 'image/jpeg', '', '4bd45b92013910f2481a665277fe03e6', 'f5ce941e27bc99e4ba52c7a34ea5ecf2d4a40314', '0', '1', '0.0.0.0', '1', '1522044075', '1', '1522044075', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('29', 'admin', '7.jpg', 'c4240e9786070638ff0d2110242e48ec.jpg', '/uploads/admin/admin/20180326/c4240e9786070638ff0d2110242e48ec.jpg', '10056', 'jpg', 'image/jpeg', '', '3e88e22e47cfa1b559d579ffad8cebef', '4bd8b62bda11c41d59240bf5051518ce8377fbfc', '0', '1', '0.0.0.0', '1', '1522044614', '1', '1522044614', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('30', 'admin', '8.jpg', '13d2ca65a7c1d07b248b489d0ebd4bf3.jpg', '/uploads/admin/admin/20180326/13d2ca65a7c1d07b248b489d0ebd4bf3.jpg', '11501', 'jpg', 'image/jpeg', '', '598d67ef56186e60642aaed0078e70c7', 'b6c5f53b177ddcd6d6641c4ce32ca50bdcb40291', '0', '1', '0.0.0.0', '1', '1522044614', '1', '1522044614', 'admin', '0');
INSERT INTO `ykb_attachment` VALUES ('31', 'admin', '7.jpg', 'cdd9bf91fd0f799cabe1160bee253795.jpg', '/uploads/admin/admin/20180326/cdd9bf91fd0f799cabe1160bee253795.jpg', '13739', 'jpg', 'image/jpeg', '', 'dcbacafb47084621cbadf3cc00a2031d', '88e1791c8e6705f25452e66fccd97c08fd108311', '0', '1', '0.0.0.0', '1', '1522045285', '1', '1522045285', 'admin', '0');

-- ----------------------------
-- Table structure for ykb_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `ykb_auth_group`;
CREATE TABLE `ykb_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `intro` varchar(30) NOT NULL DEFAULT '' COMMENT '角色介绍',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1正常，0禁用',
  `rules` text COMMENT '权限规则ID',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='权限组表';

-- ----------------------------
-- Records of ykb_auth_group
-- ----------------------------
INSERT INTO `ykb_auth_group` VALUES ('1', '超级管理员', '超级管理员', '1', '1,32,34,39,40,41,42,43,56,57,62,2,10,44,45,46,47,48,49,50,51,54,55,11,3,12,25,26,27,52,58,59,13,28,29,30,31,53,60,61,7,17,18,19,64,65,4,14');
INSERT INTO `ykb_auth_group` VALUES ('2', '内容管理员', '内容管理员', '1', '1,32,33,34,35,36,37,38,39,43,3,12,25,26,27,52,58,59,13,29,30,31,53,60,61,7,17,63');
INSERT INTO `ykb_auth_group` VALUES ('3', '测试角色', '测试', '1', null);
INSERT INTO `ykb_auth_group` VALUES ('4', '测试角色', '测试', '1', null);

-- ----------------------------
-- Table structure for ykb_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `ykb_auth_group_access`;
CREATE TABLE `ykb_auth_group_access` (
  `admin_user_id` mediumint(8) unsigned NOT NULL,
  `auth_group_id` mediumint(8) unsigned NOT NULL,
  KEY `admin_user_id` (`admin_user_id`),
  KEY `auth_group_id` (`auth_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='权限组规则表';

-- ----------------------------
-- Records of ykb_auth_group_access
-- ----------------------------
INSERT INTO `ykb_auth_group_access` VALUES ('2', '2');
INSERT INTO `ykb_auth_group_access` VALUES ('1', '1');

-- ----------------------------
-- Table structure for ykb_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `ykb_auth_rule`;
CREATE TABLE `ykb_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(20) NOT NULL,
  `description` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT ' 	1权限+菜单2只作为菜单 ',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1显示；0隐藏 ',
  `pid` smallint(5) unsigned NOT NULL COMMENT '父级ID',
  `icon` varchar(20) DEFAULT '' COMMENT '图标',
  `orders` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `condition` char(100) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='规则表';

-- ----------------------------
-- Records of ykb_auth_rule
-- ----------------------------
INSERT INTO `ykb_auth_rule` VALUES ('1', 'admin/SystemConfig/index', '系统管理', '', '2', '1', '0', 'fa-gears', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('2', 'admin/User/index', '会员管理', '', '2', '1', '0', 'fa-users', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('3', 'admin/Auth/index', '权限管理', '', '2', '1', '0', 'fa-sitemap 	', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('7', 'admin/Log/index', '记录管理', '', '2', '1', '0', 'fa-book', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('10', 'admin/AdminUser/index', '后台管理员', '', '1', '1', '2', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('11', 'admin/User/list', '前台会员', '', '1', '2', '2', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('12', 'admin/AuthRule/index', '权限列表', '', '1', '1', '3', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('13', 'admin/AuthGroup/index', '角色列表', '', '1', '1', '3', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('17', 'admin/Log/login', '登录日志', '', '1', '1', '7', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('18', 'admin/Log/admin', '管理员操作', '', '1', '1', '7', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('19', 'admin/Log/user', '会员操作', '', '1', '2', '7', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('25', 'admin/AuthRule/add', '添加权限节点', '', '1', '2', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('26', 'admin/AuthRule/edit', '修改权限节点', '', '1', '1', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('27', 'admin/AuthRule/delete', '删除权限节点', '', '1', '1', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('28', 'admin/AuthGroup/add', '添加角色类', '', '1', '2', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('29', 'admin/AuthGroup/edit', '修改角色类', '', '1', '2', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('30', 'admin/AuthGroup/delete', '删除角色类', '', '1', '2', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('31', 'admin/AuthGroup/edit_rule', '角色授权', '', '1', '2', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('32', 'admin/AdminUser/edit_password', '修改密码', '2', '1', '1', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('34', 'admin/SystemConfig/site', '网站信息', '', '1', '1', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('65', 'admin/AdminShili/index', '示例列表', '', '2', '1', '64', 'fa-asterisk', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('39', 'admin/SystemConfig/add', '添加配置项', '', '1', '2', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('40', 'admin/SystemConfig/delete', '删除配置项', '', '1', '2', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('41', 'admin/SystemConfig/edit', '修改配置项', '', '1', '2', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('42', 'admin/SyetemConfig/orders', '更新配置项排序', '', '1', '2', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('43', 'admin/SystemConfig/update_value', '单点更新配置项数值', '列表数值实时修改', '1', '2', '1', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('44', 'admin/AdminUser/add', '添加管理员', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('45', 'admin/AdminUser/edit', '修改管理员账户', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('46', 'admin/AdminUser/delete', '删除管理员账户', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('47', 'admin/AdminUser/update_status', '单点更新管理员状态值', '列表数值实时修改', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('48', 'admin/AdminUser/enable', '批量启用管理员账户', '批量更新 启用账户', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('49', 'admin/AdminUser/prohibit', '批量禁用管理员账户', '批量更新 禁用账户', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('50', 'admin/AdminUser/delete_all', '批量删除管理员账户', '批量更新 删除账户', '1', '1', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('51', 'admin/AdminUser/reset_password', '批量重置管理员账户密码 ', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('52', 'admin/AuthRule/orders', '更新权限规则排序', '', '1', '2', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('53', 'admin/AuthGroup/update_rule', '更新角色状态', '更新角色类状态 开启|禁止', '1', '1', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('54', 'admin/AdminUser/save', '提交添加管理员', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('55', 'admin/AdminUser/update', '提交修改管理员', '', '1', '2', '10', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('56', 'admin/SystemConfig/save', '提交添加配置项', '', '2', '2', '1', 'fa-gears', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('57', 'admin/SystemConfig/update', '提交修改配置项', '', '2', '2', '1', 'fa-gears', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('58', 'admin/AuthRule/save', '提交添加权限', '', '1', '2', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('59', 'admin/AuthRule/update', '提交修改权限', '', '1', '2', '12', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('60', 'admin/AuthGroup/save', '提交添加角色', '', '1', '2', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('61', 'admin/AuthGroup/update', '提交修改角色', '', '1', '1', '13', '', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('64', 'admin/AdminShili/', '示例', '', '2', '1', '0', 'fa-asterisk', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('62', 'admin/SystemConfig/home', 'Home配置', '', '2', '1', '1', 'fa-gears', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('4', 'admin/Theme/', '主题管理', '', '2', '1', '0', 'fa-desktop', '0', '');
INSERT INTO `ykb_auth_rule` VALUES ('14', 'admin/Theme/index', '主题列表', '', '1', '1', '4', '', '0', '');

-- ----------------------------
-- Table structure for ykb_log
-- ----------------------------
DROP TABLE IF EXISTS `ykb_log`;
CREATE TABLE `ykb_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '模块',
  `controller` varchar(20) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(20) NOT NULL DEFAULT '' COMMENT '方法',
  `describe` varchar(50) NOT NULL DEFAULT '' COMMENT '更新描述',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(25) NOT NULL DEFAULT '' COMMENT '账号名称',
  `add_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '操作IP',
  `city` varchar(10) NOT NULL DEFAULT '' COMMENT '城市',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_log
-- ----------------------------
INSERT INTO `ykb_log` VALUES ('1', 'admin', 'Login', 'login_username', '登录于2018-03-28 15:19:11', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522221552');
INSERT INTO `ykb_log` VALUES ('2', 'admin', 'Theme', 'update_install', '卸载主题ID:2', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522222994');
INSERT INTO `ykb_log` VALUES ('3', 'admin', 'Theme', 'update_install', '安装主题ID:', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522222997');
INSERT INTO `ykb_log` VALUES ('4', 'admin', 'Theme', 'update_status', '开启home的主题：blue', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522223000');
INSERT INTO `ykb_log` VALUES ('5', 'admin', 'Login', 'login_username', '登录于2018-03-28 15:46:29', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522223192');
INSERT INTO `ykb_log` VALUES ('6', 'admin', 'Theme', 'update_status', '开启home的主题：default', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522223256');
INSERT INTO `ykb_log` VALUES ('7', 'admin', 'Login', 'login_username', '登录于2018-03-29 15:49:45', '1', 'admin', '0.0.0.0', '内网IP', '0', '1522309786');
INSERT INTO `ykb_log` VALUES ('8', 'admin', 'Login', 'login_username', '登录于2018-04-25 09:36:48', '1', 'admin', '0.0.0.0', '内网IP', '0', '1524620208');

-- ----------------------------
-- Table structure for ykb_module
-- ----------------------------
DROP TABLE IF EXISTS `ykb_module`;
CREATE TABLE `ykb_module` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主题ID',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模块',
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否系统模块',
  `title` varchar(20) NOT NULL DEFAULT '系统模块' COMMENT '模块名',
  `intro` varchar(100) NOT NULL DEFAULT '' COMMENT '功能介绍',
  `version` varchar(20) NOT NULL DEFAULT '1.0.0' COMMENT '版本',
  `author` varchar(20) NOT NULL DEFAULT 'AndPHP_author' COMMENT '作者',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '价格，0：免费',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否默认路由：0否，1是',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态，1：启用，0：未启用',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_module
-- ----------------------------
INSERT INTO `ykb_module` VALUES ('1', 'admin', '1', '系统模块', '', '1.0.0', 'author', '0', '0', '0', '1520516142');
INSERT INTO `ykb_module` VALUES ('2', 'common', '1', '系统模块', '', '1.0.0', 'author', '0', '0', '0', '1520516142');
INSERT INTO `ykb_module` VALUES ('3', 'error', '1', '系统模块', '', '1.0.0', 'author', '0', '0', '0', '1520516142');
INSERT INTO `ykb_module` VALUES ('4', 'home', '0', '默认门户', '', '1.0.0', 'author', '0', '1', '1', '1520516142');
INSERT INTO `ykb_module` VALUES ('5', 'sns', '0', '社区模块', '社区互动', '1.0.0', 'author', '0', '0', '1', '1520516142');
INSERT INTO `ykb_module` VALUES ('6', 'shop', '0', '商城模块', '网络购物', '1.0.0', 'author', '0', '0', '1', '1520516262');
INSERT INTO `ykb_module` VALUES ('7', 'user', '0', '系统模块', '', '1.0.0', 'author', '0', '0', '1', '1520573886');

-- ----------------------------
-- Table structure for ykb_shili
-- ----------------------------
DROP TABLE IF EXISTS `ykb_shili`;
CREATE TABLE `ykb_shili` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ykb_selected` int(10) DEFAULT '0',
  `ykb_input_text` varchar(40) DEFAULT NULL,
  `ykb_input_checkbox` varchar(40) DEFAULT NULL,
  `image` varchar(200) DEFAULT NULL,
  `pics` text,
  `content` text,
  `description` varchar(255) DEFAULT NULL,
  `ykb_date` int(10) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '1正常2关闭',
  `sort` int(10) DEFAULT '50',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_shili
-- ----------------------------
INSERT INTO `ykb_shili` VALUES ('1', '1', '测试222', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', '', '0', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('2', '3', '测试110', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', '', '0', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('36', '1', '测试1', null, '/uploads/admin/admin_thumb/20180323/b9005b6d1e0a6454b3adef21ba265df0.jpg', null, '测试1内容', '测试1描述', null, null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('37', '2', '测试2', '1,2', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', '[\"\\/uploads\\/admin\\/admin\\/20180326\\/f2f66e9ef68dbb08c212473359fcffa3.jpg\",\"\\/uploads\\/admin\\/admin_thumb\\/20180323\\/38e57a3f0ea5ba8008a8ae36d2be6968.jpg\",\"\\/\\/uploads\\/admin\\/admin\\/20180326\\/49bda7791fef836aae7161d6b92a24ca.jpg\"]', '444', '发发发', null, null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('4', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('33', '2', '3', '1', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', null, '2', '1', null, null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('34', '2', '3', '1', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', null, '2', '1', null, null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('35', '2', '3', '1', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', null, '2', '1', null, null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('6', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('7', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('8', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('9', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('10', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('11', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('12', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('13', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '2', '49');
INSERT INTO `ykb_shili` VALUES ('14', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('15', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('16', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('17', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('18', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('20', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('21', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('22', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('24', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('43', '2', '123', '1', '', null, '', '', '0', null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('26', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('42', '2', '123', '1', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', '[\"\\/uploads\\/admin\\/admin\\/20180326\\/c4240e9786070638ff0d2110242e48ec.jpg\",\"\\/uploads\\/admin\\/admin\\/20180326\\/13d2ca65a7c1d07b248b489d0ebd4bf3.jpg\",\"\\/\\/uploads\\/admin\\/admin\\/20180326\\/aa0303fb1a3d4eb5fd5e7b6a64789705.jpg\"]', '43543', '12312', '0', null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('28', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('40', '2', '123', '1', '//uploads/admin/admin/20180323/b75f34c1adfcab7e25e550d0f7ae8d81.jpg', null, '43543', '12312', '0', null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('41', '2', '123', '1', '/uploads/admin/admin_thumb/20180323/82612405a119d2567020a7010d86fcc8.jpg', '[\"\\/uploads\\/admin\\/admin\\/20180326\\/c4240e9786070638ff0d2110242e48ec.jpg\",\"\\/uploads\\/admin\\/admin\\/20180326\\/13d2ca65a7c1d07b248b489d0ebd4bf3.jpg\",\"\\/\\/uploads\\/admin\\/admin\\/20180326\\/aa0303fb1a3d4eb5fd5e7b6a64789705.jpg\"]', '43543', '12312', '0', null, '1', '50');
INSERT INTO `ykb_shili` VALUES ('30', '3', '测试2', '1', '/uploads/admin/20171123/274e187632cf57b8e02bfe571f8affcc.jpg', '[\"\\/uploads\\/admin\\/20171123\\/b46bdfcc00a8d3b272641f4a162000a5.jpg\",\"\\/uploads\\/admin\\/20171123\\/5daf6a62a9bb6e831e5cab1da7dd7f4d.jpg\",\"\\/uploads\\/admin\\/20171123\\/5ba2b4cc1b56773cfd4db9fa848de337.jpg\",\"\\/uploads\\/admin\\/20171123\\/ee0f1f081d93de28f7c0928905435602.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806c4ec52f4aa71497435cf2e302441.jpg\",\"\\/uploads\\/admin\\/20171123\\/d806819fb7ab32ee40c6241438d24961.jpg\"]', '哈哈哈哈测试内容', null, '1511397840', '1511426706', '1', '50');
INSERT INTO `ykb_shili` VALUES ('31', '1', '测试', '1', '/uploads/admin/20171123/c6684affb23cccd26fc44db5b88a22b0.jpg', '[\"\\/uploads\\/admin\\/20171123\\/0ed83978b2e286386e7782cb5d222feb.jpg\",\"\\/uploads\\/admin\\/20171123\\/01a7130e4f281e61a1f610b3e6c7d840.jpg\",\"\\/uploads\\/admin\\/20171123\\/7bec50cc0689bba838f0d0dfd1ad38c9.jpg\"]', '测试内容', null, '1511397780', '1511397780', '1', '49');
INSERT INTO `ykb_shili` VALUES ('38', '2', '123', '1,2', '/uploads/admin/admin_thumb/20180323/50e883a6ae063b2aa78c52d39f0dbe02.jpg', null, '123123', '123123', '1520870400', null, '1', '48');
INSERT INTO `ykb_shili` VALUES ('39', '2', '123', '1', '//uploads/admin/admin/20180323/b75f34c1adfcab7e25e550d0f7ae8d81.jpg', null, '43543', '12312', '0', null, '1', '50');

-- ----------------------------
-- Table structure for ykb_system_config
-- ----------------------------
DROP TABLE IF EXISTS `ykb_system_config`;
CREATE TABLE `ykb_system_config` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `group` varchar(15) NOT NULL DEFAULT '' COMMENT '组类',
  `vari` varchar(32) NOT NULL,
  `value` text NOT NULL,
  `type` enum('text','textarea','file','checkbox','radio','select','checker','array','keyvalue','password','color') NOT NULL,
  `options` text NOT NULL,
  `info` text NOT NULL,
  `orders` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `vari` (`vari`),
  KEY `keyword` (`group`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_system_config
-- ----------------------------
INSERT INTO `ykb_system_config` VALUES ('1', '网站名称', 'site', 'site_title', 'yikaiba', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('2', '网站关键字1', 'site', 'site_keywords', '中国', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('3', '网站描述', 'site', 'site_description', '网站描述信息3', 'textarea', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('4', '版权描述', 'site', 'site_copyright', '方法', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('5', '统计代码', 'site', 'site_code', 'fasdd', 'textarea', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('10', 'ICP备案', 'site', 'icp', '蜀ICP备xxxxxx号', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('12', '开启审核', 'system', 'is_verify', '1', 'checker', '', '如果开启，前台数据必须审核通过以后才能显示', '0');
INSERT INTO `ykb_system_config` VALUES ('13', '审核默认值', 'system', 'default_verify', '2', 'radio', '{\"1\":\"默认审核\",\"2\":\"默认不审核\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('14', '注册邮箱认证', 'system', 'is_email_verify', '2', 'checker', '', '如果勾选，注册的时候必须通过邮箱认证', '0');
INSERT INTO `ykb_system_config` VALUES ('15', '全局分页条数', 'system', 'list_count', '15', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('16', '后台分页条数', 'system', 'admin_list_count', '15', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('17', '后台列表缓存', 'system', 'is_admin_cache', '0', 'checker', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('18', '留言是否显示列表', 'system', 'is_feedback_list', '1', 'checker', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('19', '开启栏目广告位', 'system', 'is_menu_position', '1', 'checker', '', '开启以后不同栏目可以单独创建对应栏目广告位', '0');
INSERT INTO `ykb_system_config` VALUES ('20', '是否开启Trace', 'system', 'is_trace', '1', 'checker', '', '建议程序员操作', '0');
INSERT INTO `ykb_system_config` VALUES ('21', '是否开启Debug', 'system', 'is_debug', '1', 'checker', '', '网站上线后不建议开启；建议程序员操作', '0');
INSERT INTO `ykb_system_config` VALUES ('22', '图片关联模型', 'images', 'use_picture_model', 'Album', 'checkbox', '{\"Album\":\"图集\",\"Product\":\"产品\",\"Article\":\"文章\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('23', '全局缩略图类型', 'images', 'thumb_method', '4', 'radio', '{\"1\":\"系统默认\",\"2\":\"等比例缩放\",\"3\":\"缩放后填充\",\"4\":\"居中裁剪\",\"5\":\"左上角裁剪\",\"6\":\"右下角裁剪\",\"7\":\"固定尺寸缩放\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('24', '全局缩略图宽度', 'images', 'thumb_width', '400', 'text', '', '请填写整数，单位：像素', '0');
INSERT INTO `ykb_system_config` VALUES ('25', '全局缩略图高度', 'images', 'thumb_height', '300', 'text', '', '请填写整数，单位：像素', '0');
INSERT INTO `ykb_system_config` VALUES ('26', '全局默认图片', 'images', 'default_image', '', 'file', '', '适用于列表页必须图片时，而又没有上传图片的数据', '0');
INSERT INTO `ykb_system_config` VALUES ('27', '开启图片水印', 'images', 'is_water', '2', 'checker', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('28', '水印模型', 'images', 'water_model', 'Article,Product', 'checkbox', '{\"Article\":\"文章\",\"Product\":\"产品\",\"Album\":\"图集\",\"AlbumPicture\":\"图集图片\",\"Ad\":\"广告\",\"Download\":\"下载\",\"Page\":\"单页\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('29', '水印类型', 'images', 'water_type', 'image', 'radio', '{\"text\":\"文字水印\",\"image\":\"图片水印\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('30', '水印位置', 'images', 'water_location', '9', 'radio', '{\"1\":\"左上\",\"2\":\"上居中\",\"3\":\"右上\",\"4\":\"左中\",\"5\":\"居中\",\"6\":\"右中\",\"7\":\"左下\",\"8\":\"下居中\",\"9\":\"右下\"}', '', '0');
INSERT INTO `ykb_system_config` VALUES ('31', '水印图片', 'images', 'water_image', '', 'file', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('32', '水印图片透明度', 'images', 'water_image_opacity', '100', 'text', '', '填写数值，范围1~100，100表示不透明', '0');
INSERT INTO `ykb_system_config` VALUES ('33', '水印文字', 'images', 'water_text', '', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('34', '水印文字大小', 'images', 'water_text_size', '20', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('35', '水印文字颜色', 'images', 'water_text_color', '#ffffff', 'color', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('36', '服务器地址', 'email', 'email_host', 'smtp.163.com', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('37', '发件邮箱账号', 'email', 'email_from', '', 'text', '', '应该和服务器地址对用类型', '0');
INSERT INTO `ykb_system_config` VALUES ('38', '发件账号密码', 'email', 'email_password', '', 'password', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('39', '发件人名称', 'email', 'email_fromname', 'AndPHP', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('40', 'accessKeyId', 'sms', 'sms_keyid', '', 'text', '', '填写阿里大于短信接口accessKeyId', '0');
INSERT INTO `ykb_system_config` VALUES ('42', 'appid', 'keyword', 'yt_appid', '', 'text', '', 'http://open.youtu.qq.com/申请', '0');
INSERT INTO `ykb_system_config` VALUES ('43', 'secretId', 'keyword', 'yt_secretid', '', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('44', 'secretKey', 'keyword', 'yt_secretkey', '', 'text', '', '', '0');
INSERT INTO `ykb_system_config` VALUES ('47', '储存位置', 'system', 'location', '0是个', 'text', '{\"0\":\"本地\"，\"1\":\"七牛云\"}', '文件、图片上传储存位置', '0');
INSERT INTO `ykb_system_config` VALUES ('46', '文件上传类型', 'syetem', 'file_type', 'jpg,png,gif,mp4,zip,jpeg', 'text', '', '文件上传类型', '0');
INSERT INTO `ykb_system_config` VALUES ('45', '文件上传大小', 'syetem', 'file_size', '20', 'text', '', '单位：M', '0');
INSERT INTO `ykb_system_config` VALUES ('50', '默认账户密码', 'system', 'default_password', '111111', 'text', '', '设置初始值、重置密码', '0');
INSERT INTO `ykb_system_config` VALUES ('51', 'Home主题开关', 'home', 'home_theme_status', '1', 'checker', '{\"0\":\"关闭主题\",\"1\":\"开启主题\"}', '', '0');

-- ----------------------------
-- Table structure for ykb_theme
-- ----------------------------
DROP TABLE IF EXISTS `ykb_theme`;
CREATE TABLE `ykb_theme` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主题ID',
  `module` varchar(20) NOT NULL DEFAULT '' COMMENT '所属模块',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '主题名（英文，唯一）',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '主题名，别名',
  `version` varchar(20) NOT NULL DEFAULT '1.0.0' COMMENT '版本',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '作者',
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '价格，0：免费',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态，1：启用，0：未启用',
  `create_time` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ykb_theme
-- ----------------------------
INSERT INTO `ykb_theme` VALUES ('1', 'home', 'default', '默认主题', '1.0.0', '静香', '0', '1', '1522219459');
INSERT INTO `ykb_theme` VALUES ('3', 'home', 'blue', '蓝色妖姬', '1.0.1', '蓝色妖姬', '15', '0', '1522222997');
