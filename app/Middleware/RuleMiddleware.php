<?php

declare(strict_types=1);

namespace App\Middleware;

use App\AdminRbac\CodeMsg\CommonCode;
use App\AdminRbac\Model\Admin\AdminRole;
use App\AdminRbac\Model\Rule\Rule;
use App\Exception\GeneralException;
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
        $notCheckRBAC = config('notCheckRBAC');
        if ($admin->isSuper() || in_array($currentRoute, $notCheckRBAC)) {
            return $handler->handle($request);
        }

        $adminRule = $this->adminRule($admin->id);
        $res = $this->accessRule($currentRoute, $adminRule);

        if (! $res) {
            throw new GeneralException(
                CommonCode::FO_ZE_TH_ZE_ZE_O,
                CommonCode::$errMsg[CommonCode::FO_ZE_TH_ZE_ZE_O],
                CommonCode::FOUR_HUNDRED_THREE
            );
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
