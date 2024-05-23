<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\GeneralException;
use App\Model\AdminRole;
use App\Model\Rule;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 权限验证中间件.
 */
class RuleMiddleware implements MiddlewareInterface
{
    protected HttpResponse $response;

    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    public function process(serverRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $currentRoute = '/' . strtolower($request->getMethod()) . $request->getUri()->getPath();
        $admin = admin();
        if ($admin->isSuper()) {
            return $handler->handle($request);
        }

        $adminRule = $this->adminRule($admin->id);
        $res = $this->accessRule($currentRoute, $adminRule);

        if (! $res) {
            throw new GeneralException(403001, '无权限', 403);
        }

        return $handler->handle($request);
    }

    private function adminRule($adminId): array
    {
        return AdminRole::query()
            ->from('admin_role as ar')
            ->select('ru.route', 'ru.id')
            ->crossJoin('role as r', 'ar.role_id', '=', 'r.id')
            ->crossJoin('role_rule as rr', 'r.id', '=', 'rr.role_id')
            ->crossJoin('rule as ru', 'rr.rule_id', '=', 'ru.id')
            ->where('ar.admin_id', $adminId)
            ->where('ru.type', '!=', Rule::TYPE_DIRECTORY)
            ->get()
            ->toArray();
    }

    private function accessRule($currentRoute, $adminRule): bool
    {
        if (! $currentRoute || ! $adminRule || ! is_array($adminRule)) {
            return false;
        }
        return in_array($currentRoute, array_column($adminRule, 'route'));
    }
}
