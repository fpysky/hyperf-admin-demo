<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Origin;

use App\Model\Model;

/**
 * @property int $id 
 * @property string $name 用户姓名
 * @property string $password 密码
 * @property int $status 状态：0.禁用 1.启用
 * @property int $type 类型：1超级管理员（拥有所有权限） 2 其他
 * @property string $mobile 手机号码
 * @property string $email 邮箱号码
 * @property string $last_login_ip 最近登录ip
 * @property string $logo 管理员头像
 * @property int $dept_id 部门id
 * @property int $post_id 岗位id
 * @property int $last_login_time 最后登录时间
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Admin extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'password', 'status', 'type', 'mobile', 'email', 'last_login_ip', 'logo', 'dept_id', 'post_id', 'last_login_time', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'type' => 'integer', 'dept_id' => 'integer', 'post_id' => 'integer', 'last_login_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
