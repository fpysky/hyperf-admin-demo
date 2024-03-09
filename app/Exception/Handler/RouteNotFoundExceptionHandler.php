<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\Log\Log;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 路由未找到异常处理器.
 * @package App\Exception\Handler
 */
class RouteNotFoundExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    #[Inject]
    protected RequestInterface $request;

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        /** @var NotFoundHttpException $throwable */
        $this->stopPropagation();
        $message = ErrorCode::getMessage(ErrorCode::ROUTE_NOT_FOUND);

        Log::get()->warning("路由不存在[{$this->request->getPathInfo()}]");

        return $response
            ->withBody($this->buildStdOutput($message, ErrorCode::ROUTE_NOT_FOUND))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(404);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof NotFoundHttpException;
    }
}
