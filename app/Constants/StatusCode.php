<?php

declare(strict_types=1);

namespace App\Constants;

enum StatusCode: int
{
    case Ok = 200;
    case BadRequest = 400;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case UnprocessableEntity = 422;
    case ServerError = 500;
    case BadGateway = 502;
    case GatewayTimeout = 504;
}
