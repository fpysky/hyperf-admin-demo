<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $admin_id 管理员id
 * @property int $dept_id 部门id
 */
class AdminDept extends Model
{
    public bool $timestamps = false;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_dept';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['admin_id', 'dept_id'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['admin_id' => 'integer', 'dept_id' => 'integer'];
}
