<?php
/*
 * This file is part of wulacms.
 *
 * (c) Leo Ning <windywany@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `module` (
    `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(32) NOT NULL COMMENT '模块ID',
    `version` VARCHAR(32) NOT NULL COMMENT '版本',
    `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0禁用1启用',
    `create_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '安装时间',
    `update_time` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '最后一次升级时间',
    `checkupdate` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否检测升级信息',
    `kernel` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是内核内置模块',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_NAME` (`name` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='模块表'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `group` VARCHAR(24) NOT NULL COMMENT '配置组',
    `name` VARCHAR(32) NOT NULL COMMENT '字段名',
    `value` TEXT NULL COMMENT '值',
    `auto` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否自动加载',
    `type` ENUM('T', 'J', 'A') NOT NULL DEFAULT 'T' COMMENT '值类型',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_NAME` (`group` ASC , `name` ASC),
    INDEX `IDX_AUTO` (`auto` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='配置表'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `role` (
    `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(64) NOT NULL COMMENT '角色名称',
    `note` VARCHAR(256) NULL COMMENT '说明',
    `fromids` TEXT NULL COMMENT '继承角色的ID列',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_NAME` (`name` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='用户角色'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `user` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户ID',
    `username` VARCHAR(32) NOT NULL COMMENT '用户名',
    `nickname` VARCHAR(32) NULL COMMENT '昵称',
    `lastip` VARCHAR(64) NOT NULL DEFAULT '127.0.0.1' COMMENT '上次登录IP',
    `lastlogin` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '上次登录时间',
    `status` SMALLINT UNSIGNED NOT NULL DEFAULT 1 COMMENT '1正常,0禁用,2密码过期',
    `hash` VARCHAR(255) NOT NULL COMMENT '密码HASH',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_USERNAME` (`username` ASC),
    INDEX `IDX_STATUS` (`status` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='用户表'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `user_role` (
    `user_id` INT UNSIGNED NOT NULL,
    `role_id` SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (`user_id` , `role_id`)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='用户角色表'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `user_meta` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户编号',
    `name` VARCHAR(32) NOT NULL COMMENT '数据名称',
    `value` TEXT NULL COMMENT '数据值',
    `ivalue` INT NOT NULL DEFAULT 0 COMMENT '数值型值',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_USERMETA` (`user_id` ASC , `name` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='用户元数据'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `acl` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `role_id` SMALLINT UNSIGNED NOT NULL COMMENT '角色ID',
    `op` VARCHAR(16) NOT NULL COMMENT '操作,*代表所有操作',
    `resid` CHAR(32) NOT NULL COMMENT '资源ID的MD5码',
    `res` VARCHAR(1024) NOT NULL COMMENT '资源ID',
    `allowed` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否允许',
    `priority` SMALLINT UNSIGNED NOT NULL DEFAULT 999 COMMENT '优先级,值越小优先级越高',
    `extra` TEXT NULL COMMENT '额外配置的数据，JSON格式.',
    PRIMARY KEY (`id`),
    UNIQUE INDEX `UDX_ROLE_RES` (`role_id` ASC , `op` ASC , `resid` ASC),
    UNIQUE INDEX `UDX_RESID` (`resid` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='访问控制列表'";

$tables['1.0.0'][] = "CREATE TABLE IF NOT EXISTS `syslog` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `time` INT NOT NULL COMMENT '时间',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `level` ENUM('INFO', 'WARN', 'ERROR') NULL DEFAULT 'INFO' COMMENT '级别',
    `ip` VARCHAR(64) NULL COMMENT 'IP',
    `log` TEXT NULL COMMENT '日志正文',
    PRIMARY KEY (`id`),
    INDEX `IDX_USERID` (`user_id` ASC),
    INDEX `IDX_TIME` (`time` ASC , `user_id` ASC)
)  ENGINE=INNODB DEFAULT CHARACTER SET={encoding} COMMENT='系统日志表'";

$tables['1.0.0'][] = "INSERT INTO `role` (`id`,`name`,`note`) VALUES (1,'网站所有者','拥有所有权限')";
$tables['1.0.0'][] = "INSERT INTO `role` (`id`,`name`,`note`) VALUES (2,'管理员','网站管理员')";

$tables['1.0.0'][] = "INSERT INTO `acl` (`role_id`,`op`,`resid`,`res`,`allowed`,`priority`) VALUES (1,'*','*','*',1,1)";

