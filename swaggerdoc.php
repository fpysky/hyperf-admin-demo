<?php

declare(strict_types=1);

use App\Annotation\Permission;
use App\Request\Account\ChangePasswordRequest;
use App\Request\AdminStoreRequest;
use App\Request\LoginRequest;
use App\Request\RoleStoreRequest;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

class Controller
{
    #[Post(path: 'account/changePassword', summary: '管理密码修改', tags: ['系统管理/管理员管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'password', 'newPassword', 'retNewPassword'],
        properties: [
            new Property(property: 'id', description: '管理员id', type: 'integer', example: 1),
            new Property(property: 'password', description: '原密码', type: 'string', example: '4343434'),
            new Property(property: 'newPassword', description: '重置密码', type: 'string', example: 'sdsds'),
            new Property(property: 'retNewPassword', description: '确认重置密码', type: 'string', example: 'sdsds'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '密码修改成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function changePassword() {}


    #[Post(path: 'login', summary: '登陆', tags: ['后台管理/账号'])]
    #[RequestBody(content: new JsonContent(
        required: ['username', 'password'],
        properties: [
            new Property(property: 'username', description: '用户名', type: 'string', example: '1888888888'),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['id', 'token', 'name', 'logo'],
                properties: [
                    new Property(property: 'id', description: '管理员id', type: 'integer', example: 1),
                    new Property(property: 'token', description: '登陆凭证', type: 'string', example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd'),
                    new Property(property: 'name', description: '管理员名称', type: 'string', example: '小蜜'),
                    new Property(property: 'logo', description: '管理员logo', type: 'string', example: 'http://aa.com'),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function login(LoginRequest $request){}

    #[Get(path: 'system/operateLog', summary: '操作日志列表', tags: ['系统管理/操作日志'])]
    #[Permission(name:'操作日志列表',module: '系统管理/操作日志')]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'page', description: '页码', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', schema: new Schema(type: 'integer'), example: 15)]
    #[QueryParameter(name: 'module', description: '系统模块', schema: new Schema(type: 'string'), example: '系统模块')]
    #[QueryParameter(name: 'operateType', description: '操作类型 1.新增 2.删除 3.修改 4.查询', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'operateAdmin', description: '操作人员', schema: new Schema(type: 'string'), example: '小蜜')]
    #[QueryParameter(name: 'operateStatusStr', description: '操作状态', schema: new Schema(type: 'string'), example: '成功')]
    #[QueryParameter(name: 'operateTimeStart', description: '操作时间开始(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[QueryParameter(name: 'operateTimeEnd', description: '操作时间结束(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', required: ['total', 'list'], properties: [
                new Property(property: 'total', description: '总条数', type: 'integer', example: 200),
                new Property(
                    property: 'list',
                    description: '日志列表',
                    type: 'array',
                    items: new Items(
                        required: [
                            'id', 'module', 'operateTypeZh',
                            'method', 'operateAdmin', 'operateIp',
                            'operateIpAddress','operateStatusZh', 'operatedAt',
                        ],
                        properties: [
                            new Property(property: 'id', description: '日志编号', type: 'integer', example: 1),
                            new Property(property: 'module', description: '系统模块', type: 'string', example: '系统模块'),
                            new Property(property: 'operateTypeZh', description: '操作类型中文', type: 'string', example: '操作类型中文'),
                            new Property(property: 'method', description: '请求方式', type: 'string', example: 'POST'),
                            new Property(property: 'operateAdmin', description: '操作人员', type: 'string', example: 'xxx'),
                            new Property(property: 'operateIp', description: '操作IP', type: 'string', example: '127.0.0.1'),
                            new Property(property: 'operateIpAddress', description: '操作地点', type: 'string', example: '海南省海口市'),
                            new Property(property: 'operateStatusZh', description: '操作状态中文', type: 'string', example: '成功'),
                            new Property(property: 'operatedAt', description: '操作日期(YYYY-MM-DD HH:ii:ss)', type: 'string', example: '2023-04-01 22:23'),
                        ]
                    ),
                ),
            ], type: 'object'),
        ]
    ))]
    public function operateLogList(){}

    #[Get(path: 'system/operateLog/export', summary: '操作日志列表导出', tags: ['系统管理/操作日志'])]
    #[Permission(name:'操作日志列表导出',module: '系统管理/操作日志',hasButton: true)]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'module', description: '系统模块', schema: new Schema(type: 'string'), example: '系统模块')]
    #[QueryParameter(name: 'operateType', description: '操作类型 1.新增 2.删除 3.修改 4.查询', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'operateAdmin', description: '操作人员', schema: new Schema(type: 'string'), example: '小蜜')]
    #[QueryParameter(name: 'operateStatusStr', description: '操作状态', schema: new Schema(type: 'string'), example: '成功')]
    #[QueryParameter(name: 'operateTimeStart', description: '操作时间开始(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[QueryParameter(name: 'operateTimeEnd', description: '操作时间结束(YYYY-MM-DD HH:ii:ss)', schema: new Schema(type: 'string'), example: '2023-04-01 22:23:11')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['cells', 'list', 'fileName'],
                properties: [
                    new Property(property: 'cells', description: '表头字段', type: 'array', items: new Items(
                        description: '日志编号',
                        type: 'string',
                        example: '日志编号',
                    )),
                    new Property(
                        property: 'list',
                        description: '商圈列表',
                        type: 'array',
                        items: new Items(
                            required: [
                                'id', 'module', 'operateTypeZh',
                                'method', 'operateAdmin', 'operateIp',
                                'operateIpAddress', 'operateStatusZh', 'operatedAt',
                            ],
                            properties: [
                                new Property(property: 'id', description: '日志编号', type: 'integer', example: 1),
                                new Property(property: 'module', description: '系统模块', type: 'string', example: '系统模块'),
                                new Property(property: 'operateTypeZh', description: '操作类型中文', type: 'string', example: '操作类型中文'),
                                new Property(property: 'method', description: '请求方式', type: 'string', example: 'POST'),
                                new Property(property: 'operateAdmin', description: '操作人员', type: 'string', example: 'xxx'),
                                new Property(property: 'operateIp', description: '操作IP', type: 'string', example: '127.0.0.1'),
                                new Property(property: 'operateIpAddress', description: '操作地点', type: 'string', example: '海南省海口市'),
                                new Property(property: 'operateStatusZh', description: '操作状态中文', type: 'string', example: '成功'),
                                new Property(property: 'operatedAt', description: '操作日期(YYYY-MM-DD HH:ii:ss)', type: 'string', example: '2023-04-01 22:23'),
                            ]
                        ),
                    ),
                    new Property(property: 'fileName', description: '导出文件名称', type: 'string', example: 'http://xx/xx/xx.excel'),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function operateLogExport(){}

    #[Post(path: 'admin', summary: '添加管理员', tags: ['系统管理/管理员管理'])]
    #[Permission(name:'添加管理员',module: '系统管理/管理员管理',hasButton: true)]
    #[RequestBody(content: new JsonContent(
        required: ['name', 'mobile', 'password', 'rePassword', 'email', 'deptId', 'postId', 'status'],
        properties: [
            new Property(property: 'name', description: '用户名', type: 'string', example: ''),
            new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
            new Property(property: 'password', description: '密码', type: 'string', example: 'admin123456'),
            new Property(property: 'rePassword', description: '确认密码', type: 'string', example: 'admin123456'),
            new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
            new Property(property: 'deptId', description: '部门id', type: 'array', example: [1]),
            new Property(property: 'postId', description: '职位id', type: 'integer', example: 1),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
            new Property(property: 'roleIds', description: '角色id数组', type: 'array', items: new Items(type: 'integer')),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '管理员添加成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function create(AdminStoreRequest $request){}

    #[Get(path: 'admin', summary: '管理员列表', tags: ['系统管理/管理员管理'])]
    #[Permission(name: '管理员列表', module: '系统管理/管理员管理')]
    #[QueryParameter(name: 'page', description: '页码', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'keyword', description: '搜索关键词', required: false, schema: new Schema(type: 'string'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: ['total', 'list'],
                properties: [
                    new Property(property: 'total', description: '数据总数', type: 'integer', example: 100),
                    new Property(
                        property: 'list',
                        description: '',
                        type: 'array',
                        items: new Items(
                            required: [
                                'id', 'name', 'status', 'type',
                                'mobile', 'email', 'lastLoginIp',
                                'logo', 'deptIds', 'postId', 'lastLoginTime',
                                'roleIds', 'createdAt', 'updatedAt',
                            ],
                            properties: [
                                new Property(property: 'id', description: '管理员id', type: 'integer', example: ''),
                                new Property(property: 'name', description: '姓名', type: 'string', example: ''),
                                new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                                new Property(property: 'type', description: '类型：1.超级管理员（拥有所有权限） 2.其他', type: 'integer', example: ''),
                                new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
                                new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
                                new Property(property: 'lastLoginIp', description: '最后登陆ip', type: 'string', example: ''),
                                new Property(property: 'logo', description: '头像logo', type: 'string', example: ''),
                                new Property(property: 'deptIds', description: '部门ids', type: 'integer', example: [1]),
                                new Property(property: 'roleIds', description: '部门ids', type: 'integer', example: [1]),
                                new Property(property: 'postId', description: '职位id', type: 'integer', example: ''),
                                new Property(property: 'lastLoginTime', description: '最后登陆时间', type: 'string', example: ''),
                                new Property(property: 'createdAt', description: '创建时间', type: 'string', example: ''),
                                new Property(property: 'updatedAt', description: '更新时间', type: 'string', example: ''),
                            ]
                        )
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function index(){}

    #[Get(path: 'admin/{id}', summary: '管理员详情', tags: ['系统管理/管理员管理'])]
    #[Permission(name: '管理员详情', module: '系统管理/管理员管理')]
    #[PathParameter(name: 'id', description: '管理员id', required: true, schema: new Schema(type: 'integer'), example: 1)]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '返回对象',
                required: [
                    'id', 'name', 'status', 'type',
                    'mobile', 'email', 'lastLoginIp',
                    'logo', 'deptId', 'postId',
                    'lastLoginTime', 'roleIds',
                ],
                properties: [
                    new Property(property: 'id', description: '管理员id', type: 'integer', example: ''),
                    new Property(property: 'name', description: '姓名', type: 'string', example: ''),
                    new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                    new Property(property: 'type', description: '类型：1.超级管理员（拥有所有权限） 2.其他', type: 'integer', example: ''),
                    new Property(property: 'mobile', description: '手机号', type: 'string', example: ''),
                    new Property(property: 'email', description: '电子邮箱', type: 'string', example: ''),
                    new Property(property: 'lastLoginIp', description: '最后登陆ip', type: 'string', example: ''),
                    new Property(property: 'logo', description: '头像logo', type: 'string', example: ''),
                    new Property(property: 'deptIds', description: '部门ids', type: 'array', example: [1]),
                    new Property(property: 'postId', description: '职位id', type: 'integer', example: ''),
                    new Property(property: 'lastLoginTime', description: '最后登陆时间', type: 'string', example: ''),
                    new Property(
                        property: 'roleIds',
                        description: '角色id数组',
                        type: 'array',
                        items: new Items(type: 'integer', example: 1)
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function detail(int $id){}

    #[Post(path: 'role', summary: '添加角色', tags: ['系统管理/角色管理'])]
    #[Permission(name: '添加角色', module: '系统管理/角色管理', hasButton: true)]
    #[RequestBody(content: new JsonContent(
        required: ['name', 'desc', 'status', 'sort'],
        properties: [
            new Property(property: 'name', description: '角色名', type: 'string', example: ''),
            new Property(property: 'desc', description: '描述', type: 'string', example: ''),
            new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
            new Property(property: 'sort', description: '排序', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '角色添加成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function aaaaa(RoleStoreRequest $request){}
}
