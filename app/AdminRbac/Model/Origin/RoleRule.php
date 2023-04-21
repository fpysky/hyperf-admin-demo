<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property int $role_id 角色id
 * @property int $rule_id 权限id
 */
class RoleRule extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'role_rule';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'role_id', 'rule_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'rule_id' => 'integer'];
}
