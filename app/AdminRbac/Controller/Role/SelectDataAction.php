<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class SelectDataAction extends AbstractAction
{
    #[GetMapping(path: '/role/selectData')]
    #[Get(path: '/role/selectData', summary: '角色下拉数据', tags: ['后台管理/系统管理/角色'])]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(
                property: 'data',
                description: '',
                type: 'array',
                items: new Items(
                    required: ['id', 'name'],
                    properties: [
                        new Property(property: 'id', description: '', type: 'integer', example: 1),
                        new Property(property: 'name', description: '名称', type: 'string', example: ''),
                    ]
                )
            ),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $list = Role::query()
            ->select(['id', 'name'])
            ->get();

        return $this->success($list);
    }
}
