<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * 记录未找到异常处理器.
 * @package App\Exception\Handler
 */
class ModelNotFoundExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $message = ErrorCode::getMessage(ErrorCode::MODEL_NOT_FOUND);
        $this->stopPropagation();

        return $response
            ->withBody($this->buildStdOutput($message, ErrorCode::MODEL_NOT_FOUND))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus(404);
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof ModelNotFoundException;
    }
}
