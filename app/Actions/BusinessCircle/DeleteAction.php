<?php

declare(strict_types=1);

namespace App\Actions\BusinessCircle;

use App\Actions\AbstractAction;
use App\Dao\BusinessCircle\BusinessCircleDao;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use Hyperf\Di\Annotation\Inject;
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
    #[Inject]
    protected BusinessCircleDao $businessCircleDao;

    #[DeleteMapping(path: '/businessCircle/{id:\d+}')]
    #[Delete(path: '/businessCircle/{id}', summary: '删除商圈', tags: ['后台管理/商圈管理'])]
    #[HeaderParameter(name: 'Authorization', description: '登陆凭证',required: true,example: 'Bearer eyJ0eXAiOiJqd3QifQ.eyJzd')]
    #[PathParameter(name: 'id', description: '商圈id', schema: new Schema(type:'integer'))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '操作成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(int $id): ResponseInterface
    {
        $this->businessCircleDao->delete($id);

        return $this->message('操作成功');
    }
}
