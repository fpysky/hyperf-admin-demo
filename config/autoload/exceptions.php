<?php

declare(strict_types=1);

use App\Exception\Handler\AppExceptionHandler;
use App\Exception\Handler\AuthExceptionHandler;
use App\Exception\Handler\GeneralExceptionHandler;
use App\Exception\Handler\MethodNotAllowedExceptionHandler;
use App\Exception\Handler\ModelNotFoundExceptionHandler;
use App\Exception\Handler\RouteNotFoundExceptionHandler;
use App\Exception\Handler\ValidationExceptionHandler;

return [
    'handler' => [
        'http' => [
            AuthExceptionHandler::class,
            MethodNotAllowedExceptionHandler::class,
            GeneralExceptionHandler::class,
            ModelNotFoundExceptionHandler::class,
            ValidationExceptionHandler::class,
            RouteNotFoundExceptionHandler::class,
            AppExceptionHandler::class,
        ],
    ],
];
