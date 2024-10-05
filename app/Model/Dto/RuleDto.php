<?php

namespace App\Model\Dto;

class RuleDto extends BaseDto
{
    public int $id;
    public int $parentId;
    public string $name;
    public string $icon;
    public string $route;
    public string $path;
    public int $status;
    public int $type;
    public int $sort;
}