<?php

declare(strict_types=1);

namespace App\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 主键ID
 * @property int $admin_id 管理员ID
 * @property string $province 省份名称
 * @property string $city 城市名称
 * @property int $last_login_time 登录时间
 * @property string $last_login_ip 最近登录ip
 */
class AdminLoginLog extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_login_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'admin_id', 'province', 'city', 'last_login_time', 'last_login_ip'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'admin_id' => 'integer', 'last_login_time' => 'integer'];
}
