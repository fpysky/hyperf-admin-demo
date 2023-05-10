<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\AdminRbac\Resource\AdminResource;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Database\Model\Builder;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Stringable\Str;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\QueryParameter;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ListAction extends AbstractAction
{
    #[GetMapping(path: '/admin')]
    #[Get(path: '/admin', summary: '管理员列表', tags: ['后台管理/系统管理/管理员'])]
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
                                'logo', 'deptId', 'postId', 'lastLoginTime',
                                'dept', 'createdAt', 'updatedAt',
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
                                new Property(property: 'deptId', description: '部门id', type: 'integer', example: ''),
                                new Property(property: 'postId', description: '职位id', type: 'integer', example: ''),
                                new Property(property: 'lastLoginTime', description: '最后登陆时间', type: 'string', example: ''),
                                new Property(
                                    property: 'dept',
                                    description: '部门信息',
                                    required: ['id', 'name'],
                                    properties: [
                                        new Property(property: 'id', description: '部门id', type: 'string', example: ''),
                                        new Property(property: 'name', description: '部门名称', type: 'string', example: ''),
                                    ],
                                    type: 'object',
                                ),
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
    public function index(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);
        $keyword = (string) $this->request->input('keyword');


        $builder = Admin::query()
            ->with(['dept'])
            ->orderByDesc('id');

        if (Str::length($keyword) !== 0) {
            $builder->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $paginator = $builder->paginate($pageSize);

        return $this->success([
            'list' => AdminResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }
}
