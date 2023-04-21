<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Repository\AdminOperationLogRepository;
use Carbon\Carbon;

/**
 * @property int $id 
 * @property string $module 
 * @property int $operate_type 
 * @property string $method 
 * @property int $admin_id 
 * @property string $admin_name 
 * @property string $operate_ip 
 * @property int $operate_status 
 * @property string $operated_at 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $deleted_at 
 */
class AdminOperationLog extends Model
{
    use AdminOperationLogRepository;

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
