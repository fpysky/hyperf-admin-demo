<?php

declare(strict_types=1);
use Hyperf\HttpServer\Router\Router;

/** todo::路由统一使用注解！请勿在此添加路由! */
Router::get('/favicon.ico', function () {
    return '';
});
