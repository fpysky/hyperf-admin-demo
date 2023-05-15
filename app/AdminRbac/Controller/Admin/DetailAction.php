<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DetailAction extends AbstractAction
{
    #[GetMapping(path: '/admin/{id:\d+}')]
    #[Get(path: '/admin/{id}', summary: '管理员详情', tags: ['后台管理/系统管理/管理员'])]
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
    public function handle(int $id): ResponseInterface
    {
        $admin = Admin::query()
            ->with(['adminRole','adminDept'])
            ->findOrFail($id);

        $data = [
            'id' => $admin->id,
            'name' => $admin->name,
            'status' => $admin->status,
            'type' => $admin->type,
            'mobile' => $admin->mobile,
            'email' => $admin->email,
            'lastLoginIp' => $admin->last_login_ip,
            'logo' => $admin->logo,
            'deptIds' => $admin->deptIds(),
            'postId' => $admin->post_id,
            'lastLoginTime' => $admin->last_login_time,
            'roleIds' => $admin->roleIds(),
        ];

        return $this->success($data);
    }
}
