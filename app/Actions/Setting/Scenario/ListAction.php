<?php

declare(strict_types=1);

namespace App\Actions\Setting\Scenario;

use App\Actions\AbstractAction;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Scenario;
use App\Resource\ScenarioResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HeaderParameter;
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
    #[GetMapping(path: '/scenario')]
    #[Get(path: '/scenario', summary: '场景列表', tags: ['后台管理/页面设置/场景模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'page', description: '页码', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', schema: new Schema(type: 'integer'), example: 15)]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', required: ['total', 'list'], properties: [
                new Property(property: 'total', description: '总条数', type: 'integer', example: 200),
                new Property(
                    property: 'list',
                    description: '场景列表',
                    type: 'array',
                    items: new Items(
                        required: ['id', 'title', 'subtitle', 'coverUrl', 'merchantNumStr', 'status'],
                        properties: [
                            new Property(property: 'id', description: '商圈id', type: 'integer', example: 1),
                            new Property(property: 'title', description: '地区', type: 'string', example: '海口市>龙华区'),
                            new Property(property: 'subtitle', description: '商圈名称', type: 'string', example: '滨海国际商圈'),
                            new Property(property: 'coverUrl', description: '封面图', type: 'string', example: 'http://aa.png'),
                            new Property(property: 'merchantNumStr', description: '商家数', type: 'string', example: '23.2万'),
                            new Property(property: 'status', description: '状态 0.禁用 1.启用', type: 'integer', example: 1),
                        ]
                    ),
                ),
            ], type: 'object'),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize');

        $paginator = Scenario::query()->paginate($pageSize);

        return $this->success([
            'list' => ScenarioResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }
}
