-- -----------------------------------------------------
-- Table `sys_module`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_module` ;

CREATE TABLE IF NOT EXISTS `sys_module` (
  `module` VARCHAR(40) NOT NULL COMMENT '模块',
  `category` VARCHAR(40) NULL COMMENT '分类',
  `name` VARCHAR(45) NOT NULL COMMENT '模块名称',
  `is_core` TINYINT(1) NULL DEFAULT 0,
  `version` VARCHAR(20) NULL,
  `description` VARCHAR(255) NULL,
  `url` VARCHAR(80) NULL,
  `icon` TINYINT(1) NULL DEFAULT 0 COMMENT '图标文件存在与否',
  `config` MEDIUMTEXT NULL,
  `display_order` SMALLINT NULL DEFAULT 0,
  `disabled` TINYINT(1) NULL DEFAULT 0,
  `install_date` INT(11) NULL,
  `update_date` INT(11) NULL,
  PRIMARY KEY (`module`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '模块信息表';

CREATE UNIQUE INDEX `module_UNIQUE` ON `sys_module` (`module` ASC);


-- -----------------------------------------------------
-- Table `sys_authorization`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_node` ;

CREATE TABLE IF NOT EXISTS `sys_node` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '默认标题（中文）',
  `key` VARCHAR(30) NOT NULL COMMENT '权限标识',
  `module` VARCHAR(40) NOT NULL,
  `group` VARCHAR(40) NULL,
  `category` VARCHAR(40) NULL COMMENT '分类',
  `node` VARCHAR(30) NULL,
  `type` ENUM('data','node') NULL COMMENT '权限类型',
  `description` VARCHAR(255) NULL COMMENT '说明',
  `routes` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '功能';

CREATE INDEX `fk_node_module1_idx` ON `sys_node` (`module` ASC);


-- -----------------------------------------------------
-- Table `sys_db`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_db` ;

CREATE TABLE IF NOT EXISTS `sys_db` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(40) NULL COMMENT '用户名',
  `password` VARCHAR(40) NULL COMMENT '密码',
  `host` VARCHAR(20) NULL COMMENT '服务器地址',
  `dbname` VARCHAR(40) NULL COMMENT '数据库名称',
  `status` TINYINT NULL,
  `create_time` INT UNSIGNED NULL COMMENT '创建时间',
  `memo` VARCHAR(255) NULL COMMENT '备注',
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Table `sys_cache`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_cache` ;

CREATE TABLE IF NOT EXISTS `sys_cache` (
  `cache_key` VARCHAR(60) NOT NULL,
  `class_name` VARCHAR(80) NULL,
  `key_rule` VARCHAR(60) NULL COMMENT '缓存Key规则',
  `module` VARCHAR(40) NULL,
  `description` VARCHAR(255) NULL,
  `update_time` INT(11) NULL,
  PRIMARY KEY (`cache_key`))
ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE UNIQUE INDEX `cache_key_UNIQUE` ON `sys_cache` (`cache_key` ASC);


-- -----------------------------------------------------
-- Table `sys_listener`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_listener` ;

CREATE TABLE IF NOT EXISTS `sys_listener` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_name` VARCHAR(50) NULL COMMENT '监听事件名称',
  `class_name` VARCHAR(80) NULL COMMENT '类名',
  `type` VARCHAR(15) NULL COMMENT 'cache, workflow, user',
  `module` VARCHAR(40) NULL COMMENT '所属模块',
  `description` VARCHAR(255) NULL COMMENT '描述',
  `update_time` INT UNSIGNED NULL COMMENT '更新时间',
  PRIMARY KEY (`id`))
ENGINE = MyISAM  DEFAULT CHARSET=utf8 COMMENT = '监听';

-- -----------------------------------------------------
-- Table `wedo`.`sys_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_user` ;

CREATE TABLE IF NOT EXISTS `sys_user` (
  `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(60) NULL COMMENT '邮箱，登录帐号',
  `password` CHAR(40) NULL COMMENT '密码',
  `salt` CHAR(6) NULL COMMENT '加密代码',
  `name` VARCHAR(80) NULL COMMENT '昵称',
  `gender` TINYINT(1) NULL DEFAULT 0 COMMENT '性别',
  `province` VARCHAR(30) NULL COMMENT '省份',
  `city` VARCHAR(30) NULL COMMENT '城市',
  `area` VARCHAR(30) NULL,
  `location` VARCHAR(255) NULL COMMENT '地址',
  `reg_ip` VARBINARY(16) NULL,
  `first_letter` CHAR(1) NULL,
  `ctime` INT(11) UNSIGNED NULL,
  `is_del` TINYINT(1) NULL DEFAULT 0,
  `is_active` TINYINT(1) NULL DEFAULT 0 COMMENT '是否已激活，0未激活，1激活',
  `intro` VARCHAR(255) NULL,
  `avatar` VARCHAR(255) NULL COMMENT '头像',
  PRIMARY KEY (`uid`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '用户表';

CREATE INDEX `EMAIL_INDEX` ON `sys_user` (`email` ASC);

-- -----------------------------------------------------
-- Table `sys_log`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_log` ;

CREATE TABLE IF NOT EXISTS `sys_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `req_serial_id` VARCHAR(36) NULL COMMENT '请求序列号',
  `uid` INT(11) UNSIGNED NULL DEFAULT 0 COMMENT '操作人ID',
  `module` VARCHAR(40) NULL COMMENT '模块名称',
  `log_type` VARCHAR(60) NULL COMMENT '日志类型',
  `operate_type` VARCHAR(20) NULL COMMENT '操作类型，登录、退出、浏览、添加、更新、删除',
  `description` VARCHAR(255) NULL COMMENT '操作描述',
  `create_at` INT(11) UNSIGNED NULL COMMENT '操作时间',
  `ip_address` VARBINARY(16) NULL,
  `device` VARCHAR(20) NULL COMMENT '终端设备类型',
  `os` VARCHAR(20) NULL COMMENT '操作系统',
  `browser_type` VARCHAR(20) NULL COMMENT '浏览器类型',
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '日志';

CREATE INDEX `UID_IDX` ON `sys_log` (`uid` ASC, `create_at` ASC);

-- -----------------------------------------------------
-- Table `sys_user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_user` ;

CREATE TABLE IF NOT EXISTS `sys_user` (
  `uid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(60) NULL COMMENT '邮箱，登录帐号',
  `password` CHAR(40) NULL COMMENT '密码',
  `salt` CHAR(6) NULL COMMENT '加密代码',
  `name` VARCHAR(80) NULL COMMENT '昵称',
  `gender` TINYINT(1) NULL DEFAULT 0 COMMENT '性别',
  `province` VARCHAR(30) NULL COMMENT '省份',
  `city` VARCHAR(30) NULL COMMENT '城市',
  `area` VARCHAR(30) NULL,
  `location` VARCHAR(255) NULL COMMENT '地址',
  `reg_ip` VARBINARY(16) NULL,
  `first_letter` CHAR(1) NULL,
  `ctime` INT(11) UNSIGNED NULL,
  `is_del` TINYINT(1) NULL DEFAULT 0,
  `is_active` TINYINT(1) NULL DEFAULT 0 COMMENT '是否已激活，0未激活，1激活',
  `intro` VARCHAR(255) NULL,
  `avatar` VARCHAR(255) NULL COMMENT '头像',
  PRIMARY KEY (`uid`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '用户表';

CREATE INDEX `EMAIL_INDEX` ON `sys_user` (`email` ASC);


-- -----------------------------------------------------
-- Table `sys_user_profile`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_user_profile` ;

CREATE TABLE IF NOT EXISTS `sys_user_profile` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` INT(11) UNSIGNED NOT NULL,
  `name` VARCHAR(20) NULL COMMENT '属性名',
  `value` VARCHAR(255) NULL COMMENT '属性值',
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '用户属性表';

CREATE INDEX `fk_user_profile_user1_idx` ON `sys_user_profile` (`uid` ASC);


-- -----------------------------------------------------
-- Table `sys_login_attempts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_login_attempts` ;

CREATE TABLE IF NOT EXISTS `sys_login_attempts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` CHAR(60) NOT NULL,
  `ip_address` VARBINARY(16) NULL,
  `ctime` INT(11) UNSIGNED NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE INDEX `LOGIN_INDEX` ON `sys_login_attempts` (`login` ASC);


-- -----------------------------------------------------
-- Table `sys_activation_code`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_activation_code` ;

CREATE TABLE IF NOT EXISTS `sys_activation_code` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(40) NULL,
  `type` VARCHAR(20) NULL COMMENT '类型，如注册，忘记密码等',
  `table` VARCHAR(20) NULL,
  `table_id` INT(11) UNSIGNED NULL,
  `ctime` INT(11) UNSIGNED NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '激活码';

CREATE INDEX `CODE_INDEX` ON `sys_activation_code` (`code` ASC);

CREATE INDEX `INDEX_1` ON `sys_activation_code` (`type` ASC, `table` ASC, `table_id` ASC);


-- -----------------------------------------------------
-- Table `sys_log_data`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_log_data` ;

CREATE TABLE IF NOT EXISTS `sys_log_data` (
  `id` INT(11) UNSIGNED NOT NULL,
  `req_serial_id` VARCHAR(36) NOT NULL COMMENT '请求序列号，与操作日志相同',
  `conn` VARCHAR(20) NULL COMMENT '数据库连接名',
  `table` VARCHAR(30) NULL COMMENT '表名',
  `row_id` INT(11) UNSIGNED NULL COMMENT '主键',
  `src_data` MEDIUMTEXT NULL COMMENT '操作前原数据',
  `update_data` MEDIUMTEXT NULL COMMENT '操作更新内容，只存更新项，删除和添加该值为空',
  `create_at` INT(11) UNSIGNED NULL COMMENT '操作时间',
  `operate_type` VARCHAR(20) NULL COMMENT '操作类型，add, update, delete,create,drop',
  `uid` INT(11) UNSIGNED NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8 COMMENT = '表的操作记录';


-- -----------------------------------------------------
-- Table `sys_user_account_index`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sys_user_account_index` ;

CREATE TABLE IF NOT EXISTS `sys_user_account_index` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `account` VARCHAR(60) NOT NULL COMMENT '用户名、手机号、邮箱',
  `type` CHAR(12) NULL,
  `uid` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM DEFAULT CHARSET=utf8;

CREATE INDEX `fk_sys_user_account_index_sys_user1_idx` ON `sys_user_account_index` (`uid` ASC);

CREATE UNIQUE INDEX `name_UNIQUE` ON `sys_user_account_index` (`account` ASC);

-- -----------------------------------------------------
-- Data for table `sys_user`
-- -----------------------------------------------------
INSERT INTO `sys_user` (`uid`, `email`, `password`, `salt`, `name`, `gender`, `province`, `city`, `area`, `location`, `reg_ip`, `first_letter`, `ctime`, `is_del`, `is_active`, `intro`, `avatar`) VALUES (1, 'admin@0034.com', '3fcf273f383d324ccb05aaec9fcc0ec7a0a2a67e', '558111', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL);