CREATE TABLE `demo_admin` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户姓名',
    `password` varchar(64) NOT NULL DEFAULT '' COMMENT '密码',
    `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1启用 2 停用',
    `type` tinyint unsigned NOT NULL DEFAULT '2' COMMENT '类型：1超级管理员（拥有所有权限） 2 其他',
    `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号码',
    `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱号码',
    `last_login_ip` varchar(100) NOT NULL DEFAULT '0' COMMENT '最近登录ip',
    `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '管理员头像',
    `dept_id` smallint unsigned NOT NULL DEFAULT '0' COMMENT '部门id',
    `post_id` smallint unsigned NOT NULL DEFAULT '0' COMMENT '岗位id',
    `last_login_time` int NOT NULL DEFAULT '0' COMMENT '最后登录时间',
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员表';

CREATE TABLE `demo_admin_login_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `admin_id` smallint unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `province` varchar(10) NOT NULL DEFAULT '' COMMENT '省份名称',
  `city` varchar(20) NOT NULL DEFAULT '' COMMENT '城市名称',
  `last_login_time` int unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `last_login_ip` bigint unsigned NOT NULL DEFAULT '0' COMMENT '登录IP',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员登录日志表';

CREATE TABLE `demo_admin_role` (
 `id` int unsigned NOT NULL AUTO_INCREMENT,
 `role_id` int unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
 `admin_id` int unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员角色表';

CREATE TABLE `demo_admin_visit_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `admin_id` smallint unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `ip` bigint unsigned NOT NULL DEFAULT '0' COMMENT 'IP',
  `server` varchar(5) NOT NULL DEFAULT '' COMMENT '协议',
  `method` varchar(6) NOT NULL DEFAULT '' COMMENT '方法',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '参数',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='管理员登录日志表';

CREATE TABLE `demo_dept` (
   `id` smallint unsigned NOT NULL AUTO_INCREMENT,
   `parent_id` int unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
   `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1-启用，2-禁用',
   `order` smallint unsigned NOT NULL DEFAULT '0' COMMENT '排序：按照从小到大排序',
   `name` varchar(10) NOT NULL DEFAULT '' COMMENT '部门名称',
   `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
   `username` varchar(10) NOT NULL DEFAULT '' COMMENT '用户名',
   `email` varchar(100) NOT NULL DEFAULT '' COMMENT '联系邮箱',
   `mobile` char(11) NOT NULL DEFAULT '' COMMENT '联系手机',
   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='部门表';

CREATE TABLE `demo_post` (
   `id` smallint unsigned NOT NULL AUTO_INCREMENT,
   `name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户姓名',
   `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1启用 2 停用',
   `order` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '排序：从小到大',
   `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='职位表';

CREATE TABLE `demo_role` (
   `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
   `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1 启用 2 不启用',
   `order` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '排序',
   `name` varchar(20) NOT NULL DEFAULT '' COMMENT '角色名称',
   `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='角色表';

CREATE TABLE `demo_role_rule` (
    `id` int unsigned NOT NULL AUTO_INCREMENT,
    `role_id` int unsigned NOT NULL DEFAULT '0' COMMENT '角色id',
    `rule_id` int unsigned NOT NULL DEFAULT '0' COMMENT '权限id',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='角色权限表';

CREATE TABLE `demo_rule` (
   `id` int unsigned NOT NULL AUTO_INCREMENT,
   `parent_id` int unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
   `status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态：1-启用，2-禁用',
   `type` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '类型：1-菜单，2-目录，3-按钮，4-接口',
   `order` smallint unsigned NOT NULL DEFAULT '0' COMMENT '排序：按照从小到大排序',
   `name` varchar(30) NOT NULL DEFAULT '' COMMENT '菜单名称',
   `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
   `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
   `route` varchar(100) NOT NULL DEFAULT '' COMMENT 'api请求路由名称',
   `path` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单路由path',
   `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
   `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
   `deleted_at` datetime DEFAULT NULL COMMENT '删除时间',
   PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='权限表（菜单表）';