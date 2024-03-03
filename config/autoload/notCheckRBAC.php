<?php
#免权限配置
return [
    //注：方式规则，/请求方式(小写)/请求路由的uri，如:/get/admin-user-api/Search, 或/post/admin-user-api/Search
    '/get/system/backend/backendAdminRule/menus',//菜单
    '/get/system/backend/backendAdminRule/buttons',//按钮权限列表（依照父级组合）
    '/get/system/backend/backendAdminRule/{id:\d+}',//权限详情
    '/get/system/backend/backendAdminRule/parentMenusTree',
    '/get/system/backend/backendAdminRule/ruleTree/{roleId:\d+}',
    '/get/system/backend/backendAdminRule/topRuleCombobox',
    '/get/system/backend/backendAdmin/{id:\d+}',
    '/get/system/backend/backendAdmin/deptTreeCombobox',
    '/get/system/backend/backendAdminDept/deptCombobox',
    '/get/system/backend/backendAdminPost/postCombobox',
    '/get/system/backend/backendAdminPost/{id:\d+}',
    '/get/system/backend/backendAdminRole/{id:\d+}',
    '/post/login/backend',
    '/delete/logout',
    '/post/system/backend/changePassword',
    '/get/businessCircle/selectData',
    '/get/jyOpenApi/secure/rsa/publicKey',
    '/get/system/backend/backendCos/domain',
    '/post/login',
    '/post/logout',
];
