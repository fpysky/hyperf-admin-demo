<?php

declare(strict_types=1);

use App\Middleware\CorsMiddleware;
use App\Middleware\OperateLogMiddleware;
use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        CorsMiddleware::class,
        ValidationMiddleware::class,
        OperateLogMiddleware::class,
    ],
];
