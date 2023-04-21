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

CREATE TABLE `demo_website` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '网站名称',
  `number` char(17) NOT NULL DEFAULT '' COMMENT '网站备案号',
  `company` varchar(20) NOT NULL DEFAULT '' COMMENT '公司名称',
  `address` varchar(50) NOT NULL DEFAULT '' COMMENT '公司地址',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '邮箱',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '联系电话',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `icp` char(12) NOT NULL DEFAULT '' COMMENT 'ICP证',
  `slogan` varchar(30) NOT NULL DEFAULT '' COMMENT '口号',
  `copyright` varchar(50) NOT NULL DEFAULT '' COMMENT '版权',
  `domain` varchar(30) NOT NULL DEFAULT '' COMMENT '域名',
  `public_record` char(20) NOT NULL DEFAULT '' COMMENT '公网备案',
  `tel` char(13) NOT NULL DEFAULT '' COMMENT '联系电话：如0898-65468888',
  `company_alias` varchar(10) NOT NULL DEFAULT '' COMMENT '公司简称',
  `logo` varchar(255) NOT NULL DEFAULT '' COMMENT '网站logo',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='网站基本信息及seo信息';

INSERT INTO demo_admin (id, name, password, status, type, mobile, email, last_login_ip, logo, dept_id, post_id) VALUES (1, 'admin', '7172918e6c5f4d4d6cc2f903ce6982dc', 1, 1, '18888888888', '7777@163.com', 2887617905, '', 3, 8);

INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (1, 0, 1, 1, 11, '系统权限管理', 'el-icon-s-operation', '', '', '/power/rule', '2023-04-03 13:20:50', '2023-04-04 15:48:55', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (2, 1, 1, 2, 1, '账号管理', 'document', '', 'document', '/power/admin', '2023-04-03 13:20:50', '2023-04-04 15:52:48', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (3, 1, 1, 2, 2, '角色管理', 'el-icon-document', '', '/admin/role/list', '/power/role', '2023-04-03 13:20:50', '2023-04-04 15:52:48', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (4, 1, 1, 2, 3, '权限管理', 'el-icon-document', '', '/admin/rule/list', '/power/rule', '2023-04-03 13:20:50', '2023-04-04 15:52:48', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (5, 1, 1, 2, 4, '部门管理', 'el-icon-document', '', '/admin/dept/list', '/power/dept', '2023-04-03 13:20:50', '2023-04-04 15:52:48', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (6, 1, 1, 2, 5, '岗位管理', 'el-icon-document', '', '/admin/post/list', '/power/post', '2023-04-03 13:20:50', '2023-04-04 15:52:48', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (7, 2, 1, 3, 5, '删除账号', '', '', '/del/system/backend/backendAdmin/{ids}', '', '2023-04-03 13:20:50', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (8, 2, 1, 3, 6, '启用/停用账号', '', '', '/put/system/backend/backendAdmin/status', '', '2023-04-03 13:20:50', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (9, 2, 1, 3, 7, '修改密码', '', '', '/put/system/backend/backendAdmin/pwd', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (10, 2, 1, 3, 8, '编辑账号', '', '', '/put/system/backend/backendAdmin', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (11, 2, 1, 3, 10, '新增账号', '', '', '/post/system/backend/backendAdmin', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (12, 6, 1, 3, 1, '新增岗位', '', '', '/post/system/backend/backendAdminPost', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (15, 6, 1, 3, 1, '启用/停用岗位', '', '', '/put/system/backend/backendAdminPost/status', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (18, 6, 1, 3, 1, '编辑岗位', '', '', '/put/system/backend/backendAdminPost', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (21, 5, 1, 3, 1, '新增部门', '', '', '/post/system/backend/backendAdminDept', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (25, 5, 1, 3, 1, '删除部门', '', '', '/del/system/backend/backendAdminDept/{ids}', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (29, 4, 1, 3, 1, '新增权限', '', '', '/post/system/backend/backendAdminRule', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (30, 4, 1, 3, 1, '排序权限', '', '', '/put/system/backend/backendAdminRule/batchSortRule', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (31, 3, 1, 3, 1, '新增角色', '', '', '/post/system/backend/backendAdminRole', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (36, 4, 1, 3, 1, '显示/隐藏权限', '', '', '/put/system/backend/backendAdminRule/status', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (37, 3, 1, 3, 5, '删除角色', '', '', '/del/system/backend/backendAdminRole/{ids}', '', '2023-04-03 13:20:51', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (39, 4, 1, 3, 1, '删除权限', '', '', '/del/system/backend/backendAdminRule/{ids}', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (40, 3, 1, 3, 5, '编辑角色', '', '', '/put/system/backend/backendAdminRole', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (41, 3, 1, 3, 6, '角色设置权限', '', '', '/put/system/backend/backendAdminRole/roleRule', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (43, 3, 1, 3, 9, '启用/停用角色', '', '', '/put/system/backend/backendAdminRole/status', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (45, 4, 1, 3, 1, '编辑权限', '', '', '/put/system/backend/backendAdminRule', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (47, 5, 1, 3, 1, '启用/停用部门', '', '', '/put/system/backend/backendAdminDept/status', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (48, 5, 1, 3, 1, '编辑部门', '', '', '/put/system/backend/backendAdminDept', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (50, 6, 1, 3, 1, '删除岗位', '', '', '/del/system/backend/backendAdminPost/{ids}', '', '2023-04-03 13:20:52', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (51, 2, 1, 4, 1, '列表', '', '', '/get/system/backend/backendAdmin/page', '', '2023-04-03 13:43:12', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (52, 2, 1, 4, 2, '所属部门列表', '', '', '/get/system/backend/backendAdmin/deptTreeCombobox', '', '2023-04-03 13:43:12', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (53, 2, 1, 4, 3, '角色列表', '', '', '/get/system/backend/backendAdminRole/roleCombobox', '', '2023-04-03 13:43:12', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (54, 2, 1, 4, 4, '所属岗位列表', '', '', '/get/system/backend/backendAdminPost/postCombobox', '', '2023-04-03 13:43:12', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (55, 6, 1, 4, 1, '列表', '', '', '/get/post', '', '2023-04-03 13:43:12', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (58, 6, 1, 4, 1, '权限详情', '', '', '/get/rule/info', '', '2023-04-03 13:43:13', '2023-04-04 15:54:39', null);
INSERT INTO demo_rule (id, parent_id, status, type, `order`, name, icon, `desc`, route, path, created_at, updated_at, deleted_at) VALUES (59, 5, 1, 4, 1, '编辑详情', '', '', '/get/dept/edit', '', '2023-04-03 13:43:13', '2023-04-04 15:54:39', null);