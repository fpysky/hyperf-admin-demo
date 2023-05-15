<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Model\Origin\AdminRole as Base;
use App\AdminRbac\Model\Role\Role;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property Role $role
 */
class AdminRole extends Base
{
    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
