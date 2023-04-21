<?php

declare(strict_types=1);

namespace App\Actions\Setting\Taste;

use App\Actions\AbstractAction;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Extend\Log\Log;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Model\ScenarioMerchant;
use App\Model\Taste;
use App\Model\TasteMerchant;
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
    #[PutMapping(path: '/taste')]
    #[Put(path: '/taste', summary: '更新口味', tags: ['后台管理/页面设置/口味模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[RequestBody(content: new JsonContent(
        required: ['id','title', 'subtitle', 'coverUrl','merchantIds'],
        properties: [
            new Property(property: 'id', description: '口味id', type: 'integer', example: '口味id'),
            new Property(property: 'title', description: '标题', type: 'string', example: '测试口味标题'),
            new Property(property: 'subtitle', description: '副标题', type: 'string', example: '测试口味服标题'),
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

        $taste = Taste::query()
            ->findOrFail($id);

        try {
            $taste->update([
                'title' => $title,
                'subtitle' => $subtitle,
                'cover_url' => $coverUrl,
            ]);

            TasteMerchant::query()
                ->where('taste_id', $taste->id)
                ->delete();

            foreach ($merchantIds as $merchantId) {
                ScenarioMerchant::query()->create([
                    'taste_id' => $taste->id,
                    'merchant_id' => $merchantId,
                ]);
            }
        } catch (\Throwable $throwable) {
            Log::get()->error("更新口味失败{$throwable->getMessage()}");
            throw new GeneralException(ErrorCode::SERVER_ERROR, '更新口味失败，请重试!');
        }

        return $this->message('更新成功');
    }
}
