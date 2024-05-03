<?php

declare(strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Relations\HasOne;

/**
 * @property int $id 
 * @property int $role_id 角色id
 * @property int $admin_id 用户id
 * @property-read null|Role $role 
 */
class AdminRole extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_role';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'role_id', 'admin_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'role_id' => 'integer', 'admin_id' => 'integer'];

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
