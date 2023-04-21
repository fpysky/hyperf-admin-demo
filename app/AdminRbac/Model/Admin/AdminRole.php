<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Model\Origin\AdminRole as Base;

class AdminRole extends Base
{
    public bool $timestamps = false;

    protected ?string $table = 'admin_role';
}
