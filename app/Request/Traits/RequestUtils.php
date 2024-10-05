<?php

namespace App\Request\Traits;

use App\Exception\SystemErrException;
use App\Model\Dto\BaseDto;

/**
 * @template T of BaseDto
 */
trait RequestUtils
{
    public function string(string $key, string $default = ''): string
    {
        return strval($this->input($key, $default));
    }

    public function integer(string $key, int $default = 0): int
    {
        return intval($this->input($key, $default));
    }

    public function array(string $key, array $default = []): array
    {
        return (array) $this->input($key, $default);
    }

    public function getPageSize(string $defaultKey = 'pageSize', int $default = 15): int
    {
        return $this->integer($defaultKey, $default);
    }

    public function getClientIp(): string
    {
        return $this->getHeaderLine('x-real-ip')
            ?: $this->getHeaderLine('x-forwarded-for')
                ?: $this->getServerParams()['remote_addr'] ?? '0.0.0.0';
    }

    /**
     * @return T
     */
    public function makeDto(string $dtoClass)
    {
        if (!class_exists($dtoClass)) {
            throw new SystemErrException("Class $dtoClass not found");
        }

        $dto = new $dtoClass();

        if(! $dto instanceof BaseDto){
            throw new SystemErrException("$dtoClass 类型错误 必须继承 BaseDto");
        }

        $dto->fill($this->all());

        return $dto;
    }
}