<?php

declare(strict_types=1);

namespace App\AdminRbac\Request;

use Hyperf\Validation\Request\FormRequest as Base;

class FormRequest extends Base
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
}
