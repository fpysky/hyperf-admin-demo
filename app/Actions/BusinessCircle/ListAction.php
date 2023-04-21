<?php

declare(strict_types=1);

namespace App\Actions\BusinessCircle;

use App\Actions\AbstractAction;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\BusinessCircle\BusinessCircle;
use App\Resource\BusinessCircleResource;
use Hyperf\Database\Model\Relations\HasOne;
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
use Hyperf\Utils\Str;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class ListAction extends AbstractAction
{
    #[GetMapping(path: '/businessCircle')]
    #[Get(path: '/businessCircle', summary: '商圈列表', tags: ['后台管理/商圈管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[QueryParameter(name: 'page', description: '页码', schema: new Schema(type: 'integer'), example: 1)]
    #[QueryParameter(name: 'pageSize', description: '每页显示条数', schema: new Schema(type: 'integer'), example: 15)]
    #[QueryParameter(name: 'areaId', description: '地区id', schema: new Schema(type: 'array'), example: [1, 2])]
    #[QueryParameter(name: 'name', description: '商圈名称', schema: new Schema(type: 'string'), example: '龙华区')]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象',required:['total','list'], properties: [
                new Property(property: 'total', description: '总条数', type: 'integer', example: 200),
                new Property(
                    property: 'list',
                    description: '商圈列表',
                    type: 'array',
                    items: new Items(
                        required: ['id', 'areaName', 'name', 'merchantNum', 'sort', 'createdAt'],
                        properties: [
                            new Property(property: 'id', description: '商圈id', type: 'integer', example: 1),
                            new Property(property: 'areaName', description: '地区', type: 'string', example: '海口市>龙华区'),
                            new Property(property: 'name', description: '商圈名称', type: 'string', example: '滨海国际商圈'),
                            new Property(property: 'merchantNumStr', description: '商家数', type: 'string', example: '23.2万'),
                            new Property(property: 'sort', description: '排序', type: 'integer', example: 1),
                            new Property(property: 'createdAt', description: '创建时间(YYYY-MM-DD HH:ii)', type: 'string', example: '2023-04-01 22:23'),
                        ]
                    ),
                ),
            ], type: 'object'),
        ]
    ))]
    public function handle(): ResponseInterface
    {
        $pageSize = (int) $this->request->input('pageSize');
        $areaId = (array) $this->request->input('areaId');
        $name = (string) $this->request->input('name');

        $builder = BusinessCircle::query()
            ->with(
                [
                    'area' => function (HasOne $query) {
                        $query->with(['parent']);
                    },
                ]
            );

        if (! empty($areaId)) {
            $builder->whereIn('area_id', $areaId);
        }

        if (Str::length($name) !== 0) {
            $builder->where('name', $name);
        }

        $paginator = $builder->paginate($pageSize);

        return $this->success([
            'list' => BusinessCircleResource::collection($paginator->items()),
            'total' => $paginator->total(),
        ]);
    }
}
