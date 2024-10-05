<?php

declare(strict_types=1);

namespace App\Model\Dto;

class RoleDto extends BaseDto
{
    public int $id;
    public string $name;
    public string $desc;
    public int $sort;
    public int $status;
}
