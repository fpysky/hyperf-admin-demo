<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Model;

/**
 * @property int $id ID
 * @property string $module 模块
 * @property int $operate_type 操作类型 1.新增 2.删除 3.修改 4.查询
 * @property string $method 方法
 * @property int $admin_id 操作人id
 * @property string $admin_name 操作人姓名
 * @property string $operate_ip 操作IP
 * @property int $operate_status 操作状态 0.失败 1.成功
 * @property string $operated_at 操作时间
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class AdminOperationLog extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_operation_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'module', 'operate_type', 'method', 'admin_id', 'admin_name', 'operate_ip', 'operate_status', 'operated_at', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'operate_type' => 'integer', 'admin_id' => 'integer', 'operate_status' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
