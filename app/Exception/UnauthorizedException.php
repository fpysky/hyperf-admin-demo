<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;

/**
 * 未授权访问异常.
 * @package App\Exception
 */
class UnauthorizedException extends AbstractException
{
    protected int $statusCode;

    public function __construct(string $message = '未授权的访问', $code = ErrorCode::UNAUTHORIZED, int $statusCode = 401, \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
