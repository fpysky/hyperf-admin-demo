<?php

declare(strict_types=1);

use App\AdminRbac\Model\Admin\Admin;
use App\Constants\ErrorCode;
use App\Exception\GeneralException;
use App\Extend\Log\Log;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Qbhy\HyperfAuth\AuthManager;

if (! function_exists('isProd')) {
    /**
     * 判断是否是生产环境.
     * @return bool
     * @author fengpengyuan 2022/8/11
     * @modifier fengpengyuan 2022/8/11
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
     * @author fengpengyuan 2023/3/27
     * @modifier fengpengyuan 2023/3/27
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
     * @author fengpengyuan 2023/4/10
     * @modifier fengpengyuan 2023/4/10
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
     * @author fengpengyuan 2023/4/10
     * @modifier fengpengyuan 2023/4/10
     */
    function adminId(): int
    {
        return admin()->getId();
    }
}