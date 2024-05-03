<?php

declare(strict_types=1);

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

#[Constants]
class ErrorCode extends AbstractConstants
{
    /** @Message("请求含有语义错误") */
    public const BAD_REQUEST = 400000;

    /** @Message("认证失败，未授权") */
    public const UNAUTHORIZED = 401000;

    /** @Message("登陆已过期") */
    public const TOKEN_EXPIRED = 401001;

    /** @Message("请求被拒绝") */
    public const FORBIDDEN = 403000;

    /** @Message("资源未找到") */
    public const NOT_FOUND = 404000;

    /** @Message("记录不存在") */
    public const MODEL_NOT_FOUND = 404001;

    /** @Message("路由未找到") */
    public const ROUTE_NOT_FOUND = 404002;

    /** @Message("请求方法不允许") */
    public const METHOD_NOT_ALLOWED = 405000;

    /** @Message("参数错误") */
    public const UNPROCESSABLE_ENTITY = 422000;

    /** @Message("服务器错误") */
    public const SERVER_ERROR = 500000;

    /** @Message("端参数有误") */
    public const SERVER_PLATFORM_ERROR = 500200;

    /** @Message("远程调用未知错误") */
    public const GRPC_RPC_UNKNOWN_ERROR = 500300;

    /** @Message("服务方法出错") */
    public const GRPC_RPC_SERVER_ERROR = 500301;

    /** @Message("服务节点未找到") */
    public const GRPC_RPC_NODE_NOT_FOUND = 500302;

    /** @Message("远程调用未知错误") */
    public const HTTP_RPC_SERVER_ERROR = 500500;

    /** @Message("服务方法返回结构异常") */
    public const HTTP_RPC_RESPONSE_ERROR = 500501;

    /** @Message("上游无响应") */
    public const BAD_GATEWAY = 502000;

    /** @Message("请求超时") */
    public const GATEWAY_TIMEOUT = 504000;
}
