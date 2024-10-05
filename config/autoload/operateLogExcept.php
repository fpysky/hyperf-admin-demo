<?php

declare(strict_types=1);

return [
    '/get/system/backend/backendAdminRule/buttons', // 按钮权限列表（依照父级组合）
    '/get/system/backend/backendAdminRule/{id:\d+}', // 权限详情
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
    '/get/system/operateLog',
];
