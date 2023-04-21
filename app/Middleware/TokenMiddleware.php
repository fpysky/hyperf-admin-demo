<?php

declare(strict_types=1);

namespace App\Middleware;

use App\AdminRbac\CodeMsg\CommonCode;
use App\AdminRbac\CodeMsg\TokenCode;
use App\Exception\GeneralException;
use App\Utils\RedisApi;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @deprecated 已弃用，请使用 App/Middleware/AuthMiddleware
 */
class TokenMiddleware implements MiddlewareInterface
{
    protected HttpResponse $response;

    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    public function process(serverRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $timestamp = $request->getHeaderLine('timestamp');
        $sign = $request->getHeaderLine('sign');
        $token = $request->getHeaderLine('Authorization');
        [$errCode, $errMsg, $statusCode] = $this->checkToken($timestamp, $sign, $token);

        if ($errCode) {
            throw new GeneralException($errCode,$errMsg,$statusCode);
        }

        return $handler->handle($request);
    }

    /**
     * 校验token.
     * @param $timestamp
     * @param $sign
     * @param $token
     * @return array
     */
    private function checkToken($timestamp, $sign, $token): array
    {
//        if (! $timestamp || ! $sign || ! $token) {
//            return [CommonCode::FO_ZE_ZE_ZE_ZE_O, CommonCode::$errMsg[CommonCode::FO_ZE_ZE_ZE_ZE_O], CommonCode::FOUR_HUNDRED];
//        }
//        if ($this->generateSign($timestamp) != $sign) {
//            return [TokenCode::FO_ZE_O_ZE_ZE_FI, TokenCode::$errMsg[TokenCode::FO_ZE_O_ZE_ZE_FI], CommonCode::FOUR_HUNDRED_ONE];
//        }
        $tokenValue = RedisApi::getInstance()->getToken($token);
        if (empty($tokenValue) || empty($tokenValue['uid'])) {
            return [TokenCode::FO_ZE_O_ZE_ZE_FO, TokenCode::$errMsg[TokenCode::FO_ZE_O_ZE_ZE_FO], CommonCode::FOUR_HUNDRED_ONE];
        }
        return [0, '', 0];
    }

    /**
     * 生成签名.
     * @param $timestamp
     * @return string
     */
    private function generateSign($timestamp): string
    {
        $tmp_str = $timestamp . config('myconfig.requireToken.salt');
        return md5($tmp_str);
    }
}
