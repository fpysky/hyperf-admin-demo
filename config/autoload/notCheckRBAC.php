<?php
#免权限配置
return [
    //注：方式规则，/请求方式(小写)/请求路由的uri，如:/get/api/admin-user-api/Search, 或/post/api/admin-user-api/Search
    '/get/api/system/backend/backendAdminRule/menus',//菜单
    '/get/api/system/backend/backendAdminRule/buttons',//按钮权限列表（依照父级组合）
    '/get/api/system/backend/backendAdminRule/{id:\d+}',//权限详情
    '/get/api/system/backend/backendAdminRule/parentMenusTree',
    '/get/api/system/backend/backendAdminRule/ruleTree/{roleId:\d+}',
    '/get/api/system/backend/backendAdminRule/topRuleCombobox',
    '/get/api/system/backend/backendAdmin/{id:\d+}',
    '/get/api/system/backend/backendAdmin/deptTreeCombobox',
    '/get/api/system/backend/backendAdminDept/deptCombobox',
    '/get/api/system/backend/backendAdminPost/postCombobox',
    '/get/api/system/backend/backendAdminPost/{id:\d+}',
    '/get/api/system/backend/backendAdminRole/{id:\d+}',
    '/post/api/login/backend',
    '/delete/api/logout',
    '/post/api/system/backend/changePassword',
    '/get/api/businessCircle/selectData',
    '/get/api/jyOpenApi/secure/rsa/publicKey',
    '/get/api/system/backend/backendCos/domain',
    '/post/api/login',
    '/post/api/logout',
];
