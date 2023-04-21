<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property int $parent_id 父级id
 * @property int $status 状态：1-启用，2-禁用
 * @property int $order 排序：按照从小到大排序
 * @property string $name 部门名称
 * @property string $mark 备注
 * @property string $username 用户名
 * @property string $email 联系邮箱
 * @property string $mobile 联系手机
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Dept extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'dept';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'parent_id', 'status', 'order', 'name', 'mark', 'username', 'email', 'mobile', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'parent_id' => 'integer', 'status' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
