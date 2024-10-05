<?php

declare(strict_types=1);

namespace App\Model\Dto;

class AdminDto extends BaseDto
{
    public int $id;
    public string $name;
    public string $email;
    public string $mobile;
    public string $password;
    public array $roleIds;
    public int $status;
}
