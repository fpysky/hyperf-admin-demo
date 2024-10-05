<?php

declare(strict_types=1);

use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Extend\Log\Log;
use App\Model\Admin;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Qbhy\HyperfAuth\AuthManager;

if (! function_exists('isProd')) {
    /**
     * 判断是否是生产环境.
     * @return bool
     */
    function isProd(): bool
    {
        return env('APP_ENV') == 'prod';
    }
}

if (! function_exists('jsonPrettyPrint')) {
    /**
     * 优雅的json打印.
     * @param string|array $contents
     */
    function jsonPrettyPrint(string|array $contents): void
    {
        if (is_string($contents)) {
            $contents = json_decode($contents);
        }
        echo json_encode($contents, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE) . "\n";
    }
}

if (! function_exists('admin')) {
    /**
     * 获取后台登陆用户
     * @return Admin
     */
    function admin(): Admin
    {
        $container = ApplicationContext::getContainer();
        try {
            $authManager = $container->get(AuthManager::class);
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            Log::get()->error($e->getMessage());
            throw new GeneralException(ErrorCode::SERVER_ERROR,'未知内部错误');
        }

        return $authManager->user();
    }
}

if (! function_exists('adminId')) {
    /**
     * 获取后台登陆用户id
     * @return int
     */
    function adminId(): int
    {
        return admin()->getId();
    }
}

if (! function_exists('encryptPassword')) {
    function encryptPassword(string $password): string
    {
        return password_hash($password.config('admin.password_salt'), PASSWORD_DEFAULT);
    }
}

if(! function_exists('checkPassword')){
    function checkPassword(string $password, string $passwordHash): bool
    {
        return password_verify($password.config('admin.password_salt'), $passwordHash);
    }
}