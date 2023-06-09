<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Resource\Role\RoleResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
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
    #[GetMapping(path: '/role')]
    #[Get(path: '/role', summary: '角色列表', tags: ['后台管理/系统管理/角色'])]
    #[QueryParameter(name: 'page', description: '页码', required: false, schema: new Schema(type: 'integer'))]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', required: false, schema: new Schema(type: 'integer'))]
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
                            required: ['id', 'name', 'desc', 'sort', 'status', 'createdAt', 'updatedAt'],
                            properties: [
                                new Property(property: 'id', description: 'id', type: 'integer', example: 1),
                                new Property(property: 'name', description: '名称', type: 'string', example: ''),
                                new Property(property: 'desc', description: '描述', type: 'string', example: ''),
                                new Property(property: 'sort', description: '排序', type: 'integer', example: 1),
                                new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: 1),
                                new Property(property: 'createdAt', description: '创建时间', type: 'string', example: '2023-11-11 11:11:11'),
                                new Property(property: 'updatedAt', description: '更新时间', type: 'string', example: '2023-11-11 11:11:11'),
                            ]
                        )
                    ),
                ],
                type: 'object'
            ),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize', 15);

        $paginator = Role::query()
            ->orderBy('sort')
            ->orderByDesc('id')
            ->paginate($pageSize);

        return $this->success([
            'total' => $paginator->total(),
            'list' => RoleResource::collection($paginator),
        ]);
    }
}
