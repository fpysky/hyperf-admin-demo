<?php
namespace App\Utils;

use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class RedisApi
{

    private static ?self $ins = null;
    private static $redisClient;

    private function __construct(){}
    private function __clone(){}


    public static function getInstance(): ?RedisApi
    {
        if (self::$ins === null){
            $container = ApplicationContext::getContainer();
            self::$redisClient = $container->get(RedisFactory::class)->get('token');
            self::$ins = new self();

        }
        return self::$ins;
    }

    public function setToken($key, $value, $expireTime){
        $value = json_encode($value);
        return self::$redisClient->set(config('myconfig.redisKey.token_prefix') . $key, $value, $expireTime);
    }

    public function getToken($key){
        $token =  self::$redisClient->get(config('myconfig.redisKey.token_prefix') . $key);
        return json_decode($token, true);
    }

    public function delToken($key){
        return self::$redisClient->del(config('myconfig.redisKey.token_prefix') . $key);
    }
}
