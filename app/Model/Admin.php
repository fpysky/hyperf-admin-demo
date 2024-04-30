<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Relationship\AdminRelationship;
use App\Model\Repository\AdminRepository;
use Carbon\Carbon;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\Authenticatable;

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
 * @property int $last_login_time 最后登录时间
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 * @property string $deleted_at 删除时间
 */
class Admin extends Model implements Authenticatable
{
    use AdminRelationship;
    use AdminRepository;
    use SoftDeletes;

    /** 类型：超级管理员 */
    public const TYPE_SUPER = 1;
    /** 类型：普通管理员 */
    public const TYPE_NORMAL = 2;
    /** 状态：启用 */
    public const STATUS_ENABLE = 1;
    /** 状态：禁用 */
    public const STATUS_DISABLED = 0;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'password', 'status', 'type', 'mobile', 'email', 'last_login_ip', 'logo', 'dept_id', 'last_login_time', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'type' => 'integer', 'dept_id' => 'integer', 'last_login_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }
}
