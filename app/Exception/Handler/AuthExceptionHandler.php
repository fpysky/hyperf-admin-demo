<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Constants\ErrorCode;
use App\Extend\StandardOutput\StandardOutput;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Qbhy\SimpleJwt\Exceptions\TokenExpiredException;

class AuthExceptionHandler extends ExceptionHandler
{
    use StandardOutput;

    public function handle(\Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        /** @var UnauthorizedException $throwable */
        $this->stopPropagation();
        $msg = ErrorCode::getMessage(ErrorCode::UNAUTHORIZED);

        return $response
            ->withBody($this->buildStdOutput($msg, ErrorCode::UNAUTHORIZED))
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withStatus($throwable->getStatusCode());
    }

    public function isValid(\Throwable $throwable): bool
    {
        if ($throwable instanceof UnauthorizedException || $throwable instanceof TokenExpiredException) {
            return true;
        }

        return false;
    }
}
