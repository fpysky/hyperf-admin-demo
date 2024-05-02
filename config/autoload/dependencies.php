<?php

declare(strict_types=1);

use App\Request\HttpServer\Request;
use Hyperf\HttpServer\Contract\RequestInterface;

return [
    RequestInterface::class => Request::class,
];
