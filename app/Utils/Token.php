<?php

namespace App\Utils;

class Token
{

    private static ?self $ins = null;

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function getInstance(): ?Token
    {
        if (self::$ins === null) {
            self::$ins = new self();
        }
        return self::$ins;
    }

    /**
     * 令牌写入缓存
     * @param $userId
     * @param $type
     * @return bool|string
     */
    public function saveToCache($userId, $type): bool|string
    {
        if (!$userId || !$type) {
            return false;
        }
//        $key = $this->generateToken();
        $key = $this->tokenRule($userId);
        $value = $this->prepareCachedValue($userId, $type);
        $expireTime = config('myconfig.requireToken.expireTime');
        $result = RedisApi::getInstance()->setToken($key, $value, $expireTime);
        return $result ? $key : false;
    }

    /**
     * 生成令牌
     * @return string
     */
    private function generateToken(): string
    {
        $randChar = $this->getRandChar(32);
        $timestamp = time();
        $tokenSalt = config('myconfig.requireToken.salt');
        return md5($randChar . $timestamp . $tokenSalt);
    }

    /**
     * 生成token对应的value
     * @param $user_id
     * @param $type
     * @return array
     */
    private function prepareCachedValue($user_id, $type): array
    {
        $cachedValue['uid'] = $user_id;
        $cachedValue['type'] = $type;
        return $cachedValue;
    }

    private function getRandChar($length): ?string
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }
        return $str;
    }


    /**
     * 生成用户token规则
     * @param $uid
     * @return string
     * @author zenglingkai
     * @return string
     */
    public function tokenRule($uid): string
    {
        $rule = config('myconfig.requireToken.rule');
        return $rule . $uid;
    }
}
