<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;

/**
 * 记录未找到异常.
 * @package App\Exception
 */
class RecordNotFoundException extends AbstractException
{
    public int $statusCode = 404;

    public function __construct(string $message = '记录未找到', $code = ErrorCode::NOT_FOUND, \Throwable $previous = null)
    {
        parent::__construct($message, (int) $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
