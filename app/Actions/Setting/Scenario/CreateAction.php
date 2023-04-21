<?php

declare(strict_types=1);

namespace App\Actions\Setting\Scenario;

use App\Actions\AbstractAction;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Scenario;
use App\Model\ScenarioMerchant;
use App\Request\Scenario\CreateRequest;
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
    #[PostMapping(path: '/scenario')]
    #[Post(path: '/scenario', summary: '创建场景', tags: ['后台管理/页面设置/场景模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['title', 'subtitle', 'coverUrl', 'merchantIds'],
        properties: [
            new Property(property: 'title', description: '标题', type: 'string', example: '测试场景标题'),
            new Property(property: 'subtitle', description: '副标题', type: 'string', example: '测试场景服标题'),
            new Property(property: 'coverUrl', description: '封面', type: 'string', example: 'xx/xx.jpeg'),
            new Property(property: 'merchantIds', description: '商家id集合，英文逗号分隔的字符串', type: 'string', example: '1,2'),
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
        $merchantIdsStr = (string) $request->input('merchantIds');
        $merchantIds = explode(',', $merchantIdsStr);

        $data = [
            'title' => (string) $request->input('title'),
            'subtitle' => (string) $request->input('subtitle'),
            'cover_url' => (string) $request->input('coverUrl'),
            'status' => Scenario::STATUS_ENABLE,
        ];

        $scenario = Scenario::query()->create($data);

        foreach ($merchantIds as $merchantId) {
            ScenarioMerchant::query()->create([
                'scenario_id' => $scenario->id,
                'merchant_id' => $merchantId,
            ]);
        }

        return $this->message();
    }
}
