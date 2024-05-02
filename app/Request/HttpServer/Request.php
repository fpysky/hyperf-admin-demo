<?php

declare(strict_types=1);

namespace App\Request\HttpServer;

use App\Request\Traits\RequestUtils;
use Hyperf\HttpServer\Request as Base;

class Request extends Base
{
    use RequestUtils;
}
