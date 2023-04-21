<?php

declare(strict_types=1);

namespace App\Exception;

use Hyperf\Server\Exception\ServerException;

abstract class AbstractException extends ServerException
{
    abstract public function getStatusCode(): int;
}
