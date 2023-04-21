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
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\HeaderParameter;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\PathParameter;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response;
use Hyperf\Swagger\Annotation\Schema;
use Psr\Http\Message\ResponseInterface;

#[HyperfServer('http')]
#[Controller]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class DeleteAction extends AbstractAction
{
    #[DeleteMapping(path: '/scenario/{id:\d+}')]
    #[Delete(path: '/scenario/{id}', summary: '删除场景', tags: ['后台管理/页面设置/场景模块'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证', required: true, example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[PathParameter(name: 'id', description: '场景id',required: true, schema: new Schema(type: 'integer'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '删除成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(int $id): ResponseInterface
    {
        $scenario = Scenario::query()
            ->findOrFail($id);

        try {
            Db::beginTransaction();

            ScenarioMerchant::query()
                ->where('scenario_id', $scenario->id)
                ->delete();

            $scenario->delete();

            Db::commit();
        } catch (\Exception $e) {
            Log::get()->error($e->getMessage());
            Db::rollBack();
            throw new GeneralException(ErrorCode::SERVER_ERROR, '删除失败，请重试');
        }

        return $this->message('删除成功');
    }
}
