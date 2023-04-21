<?php

declare(strict_types=1);

namespace App\Extend\StandardOutput;

use Hyperf\HttpMessage\Stream\SwooleStream;

/**
 * 标准输出组件.
 */
trait StandardOutput
{
    /**
     * 构建标准输出并返回.
     * @modifier fengpengyuan 2023/3/11
     */
    public function buildStdOutput(string $msg, int $code, mixed $data = null): SwooleStream
    {
        return new SwooleStream(json_encode($this->buildStruct($msg, $code, $data)));
    }

    /**
     * 构建标准输出并返回.
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStdOutputByThrowable(\Throwable $throwable): SwooleStream
    {
        return new SwooleStream(json_encode($this->buildStructByThrowable($throwable)));
    }

    /**
     * 构建标准输出.
     * @modifier fengpengyuan 2023/3/11
     */
    public function buildStruct(string $msg, int $code, mixed $data = null): array
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => is_null($data) ? new \stdClass() : $data,
        ];
    }

    /**
     * 构建标准输出.
     * @modifier fengpengyuan 2022/8/11
     */
    public function buildStructByThrowable(\Throwable $throwable): array
    {
        return self::buildStruct($throwable->getMessage(), $throwable->getCode());
    }
}
