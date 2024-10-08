<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Extend\CacheRule;
use App\Extend\Log\Log;
use App\Model\AdminOperationLog;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class OperateLogMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected CacheRule $cacheRule;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        $this->asyncLogOperation($request, $response->getStatusCode());

        return $response;
    }

    private function asyncLogOperation(ServerRequestInterface $request, int $statusCode): void
    {
        try {
            $admin = admin();
        } catch (\Throwable) {
            // 用户未登陆，不做记录
            return;
        }

        go(function () use ($statusCode, $admin, $request) {
            $method = $request->getMethod();
            $requestUri =  $request->getUri();
            $path = '/' . strtolower($method) . $requestUri->getPath();

            if($this->inWhitelist($path)){
                return;
            }

            // 匹配不到系统模块，不做记录
            if ($module = $this->matchModule($path)) {
                $operateIp = $xRealIp ?? $xForwardedFor ?? '';

                AdminOperationLog::query()->create([
                    'module' => $module,
                    'operate_type' => $this->convertOperateType($method),
                    'method' => $method,
                    'admin_id' => $admin->getId(),
                    'admin_name' => $admin->name,
                    'operate_ip' => $operateIp,
                    'operate_status' => $this->convertOperateStatus($statusCode),
                    'operated_at' => Carbon::now(),
                ]);
            }
        });
    }

    private function inWhitelist(string $path): bool
    {
        return in_array($path, [
            '/get/api/system/operateLog',
        ]);
    }

    private function matchModule(string $path): ?string
    {
        try {
            return $this->cacheRule->getCacheRule($path);
        } catch (\RedisException $e) {
            Log::get()->error("系统模块匹配异常:{$e->getMessage()}");
            return null;
        }
    }

    private function convertOperateStatus(int $statusCode): int
    {
        return $statusCode === StatusCode::Ok->value ? 1 : 0;
    }

    private function convertOperateType(string $method): int
    {
        switch ($method) {
            default:
            case 'GET':
                $operateType = 4;
                break;
            case 'POST':
                $operateType = 1;
                break;
            case 'DELETE':
                $operateType = 2;
                break;
            case 'PUT':
            case 'PATCH':
                $operateType = 3;
                break;
        }

        return $operateType;
    }
}
