<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Rule;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Rule\Rule;
use App\Middleware\AuthMiddleware;
use App\Resource\Rule\ButtonMenuResource;
use Hyperf\Database\Model\Relations\HasMany;
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
#[Middlewares([AuthMiddleware::class])]
class ButtonsAction extends AbstractAction
{
    #[GetMapping(path: '/rule/buttons')]
    #[Get(path: '/rule/buttons', summary: '按钮权限列表', tags: ['后台管理/系统管理/权限'])]
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
                    required: ['id', 'path', 'name'],
                    properties: [
                        new Property(property: 'id', description: 'id', type: 'integer', example: 1),
                        new Property(property: 'path', description: '菜单路由path', type: 'string', example: ''),
                        new Property(property: 'name', description: '名称', type: 'string', example: ''),
                        new Property(
                            property: 'buttons',
                            description: '按钮权限列表',
                            type: 'array',
                            items: new Items(
                                required: ['id', 'name', 'status', 'icon', 'route', 'path', 'roles'],
                                properties: [
                                    new Property(property: 'id', description: '', type: 'integer', example: ''),
                                    new Property(property: 'name', description: '名称', type: 'string', example: ''),
                                    new Property(property: 'status', description: '状态：0.禁用 1.启用', type: 'integer', example: ''),
                                    new Property(property: 'icon', description: '图标', type: 'string', example: ''),
                                    new Property(property: 'route', description: '请求路由', type: 'string', example: ''),
                                    new Property(property: 'path', description: '菜单路由path', type: 'string', example: ''),
                                    new Property(property: 'roles', description: '角色列表', type: 'array', items: new Items(type: 'string', example: 'admin')),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $menuButtons = Rule::query()
            ->select(['id','parent_id','path','name'])
            ->with([
                'buttons' => function (HasMany $query) {
                    $query->with([
                        'roleRule' => function (HasMany $hasMany) {
                            $hasMany->with('role');
                        },
                    ]);
                },
            ])
            ->where('type', Rule::TYPE_MENU)
            ->get();

        return $this->success(ButtonMenuResource::collection($menuButtons));
    }
}
