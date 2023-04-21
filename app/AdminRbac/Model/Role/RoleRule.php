<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Role;

use App\AdminRbac\Model\Origin\RoleRule as Base;
use App\AdminRbac\Model\Rule\Rule;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property Role $role
 * @property Rule $rule
 */
class RoleRule extends Base
{
    public bool $timestamps = false;

    public function rule(): BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_id', 'id');
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
