<?php

declare(strict_types=1);

namespace App\Constants;

enum BaseCode: int implements CodeInterface
{
    case Ok = 200000;
    case BadRequest = 400000;
    case Unauthorized = 401000;
    case Forbidden = 403000;
    case NotFound = 404000;
    case ModelNotFound = 404001;
    case RouteNotFound = 404002;
    case MethodNotAllowed = 405000;
    case UnprocessableEntity = 422000;
    case ServerError = 500000;
    case GrpcRpcUnknownError = 500300;
    case GrpcRpcServerError = 500301;
    case GrpcRpcNodeNotFound = 500302;
    case JsonRpcServerError = 500400;
    case JsonRpcMethodNotFound = 500401;
    case JsonRpcInvalidRequest = 500402;
    case JsonRpcInvalidParams = 500403;
    case JsonRpcParseError = 500404;
    case JsonRpcInternalError = 500405;
    case HttpRpcServerError = 500500;
    case HttpRpcResponseError = 500501;
    case BadGateway = 502000;
    case GatewayTimeout = 504000;

    public function getMessage(): string
    {
        return match ($this) {
            self::Ok => '请求成功',
            self::BadRequest => '请求含有语义错误',
            self::Unauthorized => '认证失败，未授权',
            self::Forbidden => '请求被拒绝',
            self::NotFound => '资源未找到',
            self::ModelNotFound => '记录未找到',
            self::RouteNotFound => '路由未找到',
            self::MethodNotAllowed => '请求方法不允许',
            self::UnprocessableEntity => '参数错误',
            self::ServerError => '服务器错误',
            self::GrpcRpcUnknownError,self::JsonRpcServerError, self::HttpRpcServerError => '远程调用未知错误',
            self::GrpcRpcServerError => '服务方法出错',
            self::GrpcRpcNodeNotFound => '服务节点未找到',
            self::JsonRpcMethodNotFound => '服务方法未找到',
            self::JsonRpcInvalidRequest => '非法的服务方法请求',
            self::JsonRpcInvalidParams => '服务方法请求参数错误',
            self::JsonRpcParseError => '服务方法解析错误',
            self::JsonRpcInternalError => '服务方法内部错误',
            self::HttpRpcResponseError => '服务方法返回结构异常',
            self::BadGateway => '上游无响应',
            self::GatewayTimeout => '请求超时',
        };
    }
}
