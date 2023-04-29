<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Role;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Role\Role;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Request\Role\SetRuleRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
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
class SetRuleAction extends AbstractAction
{
    /**
     * @throws \Exception
     */
    #[PostMapping(path: '/role/setRule')]
    #[Post(path: '/role/setRule', summary: '角色设置权限', tags: ['后台管理/系统管理/角色'])]
    #[RequestBody(content: new JsonContent(
        required: ['ruleIds', 'roleId'],
        properties: [
            new Property(property: 'ruleIds', description: '权限id数组', type: 'array', example: [1, 2]),
            new Property(property: 'roleId', description: '角色id', type: 'integer', example: 1),
        ]
    ))]
    #[Response(response: 200, content: new JsonContent(
        required: ['code', 'msg', 'data'],
        properties: [
            new Property(property: 'code', description: '业务状态码', type: 'integer', example: 200000),
            new Property(property: 'msg', description: '返回消息', type: 'string', example: '权限分配成功'),
            new Property(property: 'data', description: '返回对象', type: 'object'),
        ]
    ))]
    public function handle(SetRuleRequest $request): ResponseInterface
    {
        $ruleIds = (array) $request->input('ruleIds');
        $roleId = (int) $request->input('roleId');

        Role::findFromCacheOrFail($roleId)
            ->setRule($ruleIds);

        return $this->message('权限分配成功');
    }
}
