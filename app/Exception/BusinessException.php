<?php

declare(strict_types=1);

namespace App\Exception;

use App\Constants\ErrorCode;

class BusinessException extends AbstractException
{
    protected int $statusCode = 500;

    public function __construct(string $message, int $code = ErrorCode::SERVER_ERROR, int $statusCode = 500, \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
