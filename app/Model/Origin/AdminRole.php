<?php

declare(strict_types=1);

namespace App\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property int $role_id 角色id
 * @property int $admin_id 用户id
 */
class AdminRole extends Model
{
    public bool $timestamps = false;
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
}
