<?php

declare(strict_types=1);

namespace App\Actions\Setting\Scenario;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Extend\Log\Log;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\Scenario;
use App\Model\ScenarioMerchant;
use App\Request\Scenario\UpdateRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Hyperf\Swagger\Annotation\Response;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class UpdateAction extends AbstractAction
{
    #[PutMapping(path: '/scenario')]
    #[Put(path: '/scenario', summary: '更新场景', tags: ['后台管理/页面设置/场景模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id', 'title', 'subtitle', 'coverUrl', 'merchantIds'],
        properties: [
            new Property(property: 'id', description: '场景id', type: 'integer', example: 1),
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
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '更新成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(UpdateRequest $request): ResponseInterface
    {
        $id = (int) $request->input('id');
        $title = (string) $request->input('title');
        $subtitle = (string) $request->input('subtitle');
        $coverUrl = (string) $request->input('coverUrl');
        $merchantIdsStr = (string) $request->input('merchantIds');
        $merchantIds = explode(',', $merchantIdsStr);

        $scenario = Scenario::query()
            ->findOrFail($id);

        try {
            $scenario->update([
                'title' => $title,
                'subtitle' => $subtitle,
                'cover_url' => $coverUrl,
            ]);

            ScenarioMerchant::query()
                ->where('scenario_id', $scenario->id)
                ->delete();

            foreach ($merchantIds as $merchantId) {
                ScenarioMerchant::query()->create([
                    'scenario_id' => $scenario->id,
                    'merchant_id' => $merchantId,
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::get()->error("更新场景失败{$throwable->getMessage()}");
            throw new GeneralException(ErrorCode::SERVER_ERROR, '更新场景失败，请重试!');
        }

        return $this->message('更新成功');
    }
}
