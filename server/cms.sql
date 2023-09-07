
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cms_activity
-- ----------------------------
DROP TABLE IF EXISTS `cms_activity`;
CREATE TABLE `cms_activity`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `des` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '简述',
  `alt` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '封面alt',
  `address` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '活动地址',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容详情',
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `sort_id` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `start_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '结束时间',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '活动列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_activity
-- ----------------------------
INSERT INTO `cms_activity` VALUES (1, '最新活动', '[{\"name\":\"about6.png\",\"path\":\"activity/20221111\\\\1eaf94641f3e757071b29909f9072a00.png\"}]', '最新活动最新活动最新活动最新活动最新活动', '最新活动', '广州大学', '<p>最新活动最新活动最新活动最新活动最新活动最新活动最新活动最新活动最新活动最新活动</p>', 1000, 0, 1668124800, 1668211200, 1668154344, 1670574435, 0);

-- ----------------------------
-- Table structure for cms_activity_apply
-- ----------------------------
DROP TABLE IF EXISTS `cms_activity_apply`;
CREATE TABLE `cms_activity_apply`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `activity_id` int(11) NOT NULL COMMENT '活动ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `sex` tinyint(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 2 COMMENT '性别 0女 1男 2保密',
  `phone_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '电话前缀',
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电话',
  `wechat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信',
  `mail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `school` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '学校',
  `address` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '居住地址',
  `book` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '取通知书',
  `screen` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '录屏图片',
  `referer` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问来源',
  `device` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '访问设备',
  `audit` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核 0未审核 1已审核',
  `audit_content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '审核内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '参加活动表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_activity_apply
-- ----------------------------
INSERT INTO `cms_activity_apply` VALUES (1, 0, 'name', 2, NULL, '1336790861', '1337897971', '', NULL, 'address', '', 'content', '', 'pc', 1, '已审核', 1666943414, 1666943414, 0);
INSERT INTO `cms_activity_apply` VALUES (2, 2, '曾先生', 2, '+852', '13345679798', 'we123456', '', '香港大学', '', 'activity/20221112\\2d2bc589193dcd69fbf1f5634ff472d0.png', 'activity/20221112\\1b242760206526591923dcbfb2c8df53.png', 'http://www.green6d.test/activity_2.html', 'pc', 1, '审核通过', 1668245672, 0, 0);
INSERT INTO `cms_activity_apply` VALUES (3, 1, 'jdjdj', 2, '+86', '13345645678', '4546546', '', 'Poly U', '', '', '', 'http://www.green6d.com/activity_1.html', 'pc', 1, '审核通过', 1669360631, 0, 0);

-- ----------------------------
-- Table structure for cms_admin_login
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_login`;
CREATE TABLE `cms_admin_login`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `uip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '认证ip',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态： 0 失败 1 成功',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '登录类型：1 登录 2注销',
  `content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容描述',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE COMMENT 'username 普通索引'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '后台登录日志' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_admin_login
-- ----------------------------

-- ----------------------------
-- Table structure for cms_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_menu`;
CREATE TABLE `cms_admin_menu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `pid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级菜单',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `label` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标签名称',
  `path` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '路由地址',
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '组件地址',
  `icon` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dashboard' COMMENT '图标',
  `hidden` tinyint(4) NOT NULL DEFAULT 0 COMMENT '菜单是否显示 0 显示 1 隐藏',
  `sort_id` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '后台菜单' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_admin_menu
-- ----------------------------
INSERT INTO `cms_admin_menu` VALUES (1, 0, '系统管理', 'System', '/system', '', 's-help', 0, 10, '系统管理');
INSERT INTO `cms_admin_menu` VALUES (2, 1, '管理员', 'Admin', 'admin', 'system/admin', '', 0, 100, '管理员');
INSERT INTO `cms_admin_menu` VALUES (3, 1, '菜单', 'Menu', 'menu', 'system/menu', '', 0, 120, '');
INSERT INTO `cms_admin_menu` VALUES (4, 1, '角色', 'Role', 'role', 'system/role', '', 0, 130, '');
INSERT INTO `cms_admin_menu` VALUES (5, 1, '登录日志', 'LoginLog', 'login-log', 'system/login-log', '', 0, 140, '');
INSERT INTO `cms_admin_menu` VALUES (6, 1, '操作日志', 'PlayLog', 'play-log', 'system/play-log', '', 0, 150, '');
INSERT INTO `cms_admin_menu` VALUES (7, 0, '资讯管理', 'News', '/news', '', 's-platform', 0, 40, '');
INSERT INTO `cms_admin_menu` VALUES (17, 7, '资讯列表', 'Article', 'article', 'news/article', '', 0, 1000, '');
INSERT INTO `cms_admin_menu` VALUES (18, 0, '咨询管理', 'Consult', '/consult', '', 'chat-line-square', 0, 1100, '');
INSERT INTO `cms_admin_menu` VALUES (19, 18, '咨询列表', 'ConsultIndex', 'index', 'consult/index', '', 0, 1000, '');
INSERT INTO `cms_admin_menu` VALUES (20, 0, '活动管理', 'Activity', '/activity', '', 's-flag', 0, 1000, '');
INSERT INTO `cms_admin_menu` VALUES (21, 20, '报名列表', 'Apply', 'apply', 'activity/apply', '', 0, 1000, '');
INSERT INTO `cms_admin_menu` VALUES (22, 20, '活动列表', 'Index', 'index', 'activity/index', '', 0, 900, '');
INSERT INTO `cms_admin_menu` VALUES (23, 0, '问题管理', 'Faq', '/faq', '', 'question', 0, 1000, '');
INSERT INTO `cms_admin_menu` VALUES (24, 23, '常见问题', 'FaqIndex', 'index', 'faq/index', '', 0, 1000, '');

-- ----------------------------
-- Table structure for cms_admin_play
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_play`;
CREATE TABLE `cms_admin_play`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `action` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作模块',
  `url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'URL',
  `uip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '操作IP',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态： 0 失败 1 成功',
  `content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容描述',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE COMMENT 'username 普通索引'
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '后台登录日志' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_admin_play
-- ----------------------------

-- ----------------------------
-- Table structure for cms_admin_role
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_role`;
CREATE TABLE `cms_admin_role`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '角色是否启动 0 禁止 1 启用',
  `menu` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '权限集合',
  `content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '内容描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '后台角色' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_admin_role
-- ----------------------------
INSERT INTO `cms_admin_role` VALUES (1, '超级管理员', 1, '', '超级管理员拥有系统全部操作权限');
INSERT INTO `cms_admin_role` VALUES (2, '內容管理角色', 1, '10,11,12,13,17,20,21,22,23,24,25,26,27,28,30,7', '內容管理角色');

-- ----------------------------
-- Table structure for cms_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `cms_admin_user`;
CREATE TABLE `cms_admin_user`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `token` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '认证token',
  `uip` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '认证ip',
  `nickname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `phone` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '手机号码',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/static/admin/images/avatar.png' COMMENT '头像',
  `role` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1' COMMENT '角色',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否启用 0：否 1：是',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `username`(`username`) USING BTREE COMMENT 'username 普通索引'
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '后台用户' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_admin_user
-- ----------------------------
INSERT INTO `cms_admin_user` VALUES (1, 'admin', '07d61403c16d59432e4b2aa79a12e6f64cdc8d4d', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE2OTQwNjYzNjksIm5iZiI6MTY5NDA2NjM2OSwiZXhwIjoxNjk2NjU4MzY5LCJ1aWQiOjEsInVpcCI6IjEyNy4wLjAuMSJ9.p-ES9MamTUReGOH6RQ488V7mahXRAyDhMFDhmKN0He4', '127.0.0.1', '超级管理员', '13345678905', '/static/admin/images/avatar.png', '1', 1);

-- ----------------------------
-- Table structure for cms_consult
-- ----------------------------
DROP TABLE IF EXISTS `cms_consult`;
CREATE TABLE `cms_consult`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `sex` tinyint(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 2 COMMENT '性别 0女 1男 2保密',
  `phone_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '电话前缀',
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电话',
  `wechat` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '微信',
  `mail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `school` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '学校',
  `price` int(11) NULL DEFAULT NULL COMMENT '期望价格',
  `referer` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '访问来源',
  `device` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '访问设备',
  `content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '内容描述',
  `audit` tinyint(1) NOT NULL DEFAULT 0 COMMENT '审核 0未审核 1已审核',
  `audit_content` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '审核内容',
  `create_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NOT NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `phone`(`phone`) USING BTREE,
  INDEX `create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '咨询表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_consult
-- ----------------------------
INSERT INTO `cms_consult` VALUES (1, 'asdag', 1, '+852', '13356797651', 'asg', 'ga', NULL, 320, 'gas', 'pc', 'ga', 1, '已审核', 1666943414, 1666943414, 0);
INSERT INTO `cms_consult` VALUES (2, '测试', 1, '+852', '13356797651', 'wechat', 'hioubpou@qq.com', NULL, 320, 'http://www.green6d.test/', 'pc', 'content', 0, '', 0, 1668145291, 1668145291);
INSERT INTO `cms_consult` VALUES (3, '曾先生', 2, '+852', '13367972891', 'weh123', '', '', 320, 'http://www.green6d.test/housing_1.html', 'pc', '学校附近', 0, '', 1668137243, 1670551200, 1670551200);

-- ----------------------------
-- Table structure for cms_faq
-- ----------------------------
DROP TABLE IF EXISTS `cms_faq`;
CREATE TABLE `cms_faq`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `des` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '简述',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容详情',
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `sort_id` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '常见问题表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_faq
-- ----------------------------
INSERT INTO `cms_faq` VALUES (1, 'Q：安装', '', '<h2>在您自己的 PC 机上建立 PHP</h2>\n<p>然而，如果您的服务器不支持 PHP，您必须：</p>\n<ul>\n<li>安装 Web 服务器</li>\n<li>安装 PHP</li>\n<li>安装数据库，比如 MySQL</li>\n</ul>\n<p>官方 PHP 网站（PHP.net）有 PHP 的安装说明：&nbsp;<a href=\"http://php.net/manual/en/install.php\" target=\"_blank\" rel=\"noopener noreferrer\">http://php.net/manual/en/install.php</a></p>', 1000, 0, 1670212742, 1667275039, 0);

-- ----------------------------
-- Table structure for cms_news
-- ----------------------------
DROP TABLE IF EXISTS `cms_news`;
CREATE TABLE `cms_news`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标题',
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT '封面地址',
  `alt` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT '' COMMENT '封面alt',
  `des` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '简述',
  `url` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '公众号URl',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容详情',
  `views` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `sort_id` int(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序',
  `create_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(11) UNSIGNED NULL DEFAULT 0 COMMENT '修改时间',
  `delete_time` int(11) NULL DEFAULT 0 COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '资讯文章表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cms_news
-- ----------------------------
INSERT INTO `cms_news` VALUES (1, 'php是世界上最好的语言', '[{\"name\":\"about5.png\",\"path\":\"news/20221104/e10b1dd833f577f006bb861133a3484c.png\"}]', 'php是世界上最好的语言', 'PHP 是一种创建动态交互性站点的强有力的服务器端脚本语言。', NULL, '<div class=\"vc_row wpb_row vc_row-fluid\">\n<div class=\"wpb_column vc_column_container vc_col-sm-12\">\n<div class=\"vc_column-inner\">\n<div class=\"wpb_wrapper\">\n<div class=\"wpb_video_widget wpb_content_element vc_clearfix   vc_video-aspect-ratio-169 vc_video-el-width-100 vc_video-align-left\">\n<div class=\"wpb_wrapper\">\n<p>PHP 是一种创建动态交互性站点的强有力的服务器端脚本语言。</p>\n<p>PHP 是免费的，并且使用非常广泛。同时，对于像微软 ASP 这样的竞争者来说，PHP 无疑是另一种高效率的选项。</p>\n<p><a href=\"https://www.runoob.com/w3cnote/php-learning-recommend.html\" target=\"_blank\" rel=\"noopener noreferrer\"><strong>适用于PHP初学者的学习线路和建议</strong></a></p>\n<p><a href=\"https://www.runoob.com/w3cnote/php-develop-tools.html\" target=\"_blank\" rel=\"noopener noreferrer\"><strong>PHP 开发工具推荐</strong></a></p>\n<p><a href=\"https://www.runoob.com/try/runcode.php?filename=demo_intro&amp;type=php\" target=\"_blank\" rel=\"noopener noreferrer\"><strong>PHP 在线工具</strong></a></p>\n<p><img src=\"http://www.admin.test/storage/tinymce/20230907/347191fca777f523ff96f64f6317c97c.jpg\" alt=\"\" width=\"854\" height=\"92\" /></p>\n</div>\n</div>\n</div>\n</div>\n</div>\n</div>', 1000, 0, 1666668391, 1669970008, 0);

SET FOREIGN_KEY_CHECKS = 1;
