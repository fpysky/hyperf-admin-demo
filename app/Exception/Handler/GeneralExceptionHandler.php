<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\AbstractException;
use App\Extend\Log\Log;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * 通用异常处理器.
 * @package App\Exception\Handler
 */
class GeneralExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        /** @var AbstractException $throwable */
        $this->stopPropagation();

        Log::get()->warning($throwable->getMessage(), [
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
            'trace' => $throwable->getTraceAsString(),
        ]);

        return $response
            ->withBody($this->buildStdOutputByThrowable($throwable))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($throwable->getStatusCode());
    }

    public function isValid(\Throwable $throwable): bool
    {
        return $throwable instanceof AbstractException;
    }
}
