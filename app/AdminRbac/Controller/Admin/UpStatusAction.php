<?php

declare(strict_types=1);

namespace App\AdminRbac\Controller\Admin;

use App\Actions\AbstractAction;
use App\AdminRbac\Model\Admin\Admin;
use App\Exception\UnprocessableEntityException;
use App\Extend\Auth\AuthManager;
use App\Middleware\AuthMiddleware;
use App\Middleware\RuleMiddleware;
use App\Request\Admin\UpStatusRequest;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\Swagger\Annotation\HyperfServer;
use Psr\Http\Message\ResponseInterface;
use Qbhy\SimpleJwt\Exceptions\InvalidTokenException;
use Qbhy\SimpleJwt\Exceptions\SignatureException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;

#[HyperfServer('http')]
#[Controller(prefix: 'admin')]
#[Middlewares([AuthMiddleware::class, RuleMiddleware::class])]
class UpStatusAction extends AbstractAction
{
    #[Inject]
    protected AuthManager $auth;

    /**
     * @throws \RedisException
     * @throws SignatureException
     * @throws InvalidTokenException
     * @throws TokenExpiredException
     */
    #[PutMapping(path: '/status')]
    public function handle(UpStatusRequest $request): ResponseInterface
    {
        $ids = (array) $request->input('ids');
        $status = (int) $request->input('status');

        if (Admin::hasSuperAdmin($ids)) {
            throw new UnprocessableEntityException('不能禁用超级管理员');
        }

        Admin::query()
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        // 禁用时，强制退出
        if ($status === Admin::STATUS_DISABLED) {
            $this->auth->batchLogoutByAdmin($ids);
        }

        $action = $status === Admin::STATUS_ENABLE ? '启用' : '禁用';

        return $this->success("管理员{$action}成功");
    }
}
