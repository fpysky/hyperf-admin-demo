<?php

declare(strict_types=1);

namespace App\Extend\Log;

use Hyperf\Context\ApplicationContext;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class Log
{
    public static function get(string $name = 'xxxx'): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($name);
    }

    public static function errLogFromException(string $msg,\Throwable $exception): void
    {
        static::get()
            ->error("$msg:{$exception->getMessage()}",[
                'trace' => $exception->getTraceAsString(),
            ]);
    }
}
