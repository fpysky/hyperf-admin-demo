<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Psr\Http\Message\ResponseInterface;

/**
 * 路由未找到异常处理器.
 * @package App\Exception\Handler
 */
class RouteNotFoundExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        /** @var NotFoundHttpException $throwable */
        $this->stopPropagation();
        $message = ErrorCode::getMessage(ErrorCode::ROUTE_NOT_FOUND);

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
