<?php

namespace App\Request\Traits;

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

    public function getPageSizeOrDefault(string $defaultKey = 'pageSize',int $default = 15): int
    {
        return $this->integer($defaultKey, $default);
    }
}