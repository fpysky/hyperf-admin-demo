<?php

declare(strict_types=1);

namespace App\Actions\BusinessCircle;

use App\Actions\AbstractAction;
use App\Dao\BusinessCircle\BusinessCircleDao;
use App\Exception\UnprocessableEntityException;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\BusinessCircle\BusinessCircle;
use App\Request\BusinessCircle\CreateRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class CreateAction extends AbstractAction
{
    #[Inject]
    protected BusinessCircleDao $businessCircleDao;

    #[PostMapping(path: '/businessCircle')]
    #[Post(path: '/businessCircle', summary: '创建商圈', tags: ['后台管理/商圈管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['areaId', 'name', 'sort'],
        properties: [
            new Property(property: 'areaId', description: '地区id', type: 'integer', example: 1),
            new Property(property: 'name', description: '商圈名称', type: 'string', example: '龙华区'),
            new Property(property: 'sort', description: '地区id', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: ''),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(CreateRequest $request): ResponseInterface
    {
        $areaId = (int) $request->input('areaId');
        $name = (string) $request->input('name');
        $sort = (int) $request->input('sort');

        if (BusinessCircle::existsByName($name)) {
            throw new UnprocessableEntityException('商圈已存在');
        }

        $this->businessCircleDao->create($areaId, $name, $sort);

        return $this->message();
    }
}
