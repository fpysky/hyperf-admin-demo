<?php

declare(strict_types=1);

namespace App\Model;

use App\Model\Repository\AdminRepository;
use App\Model\Vo\AdminVo;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\Relations\BelongsTo;
use Hyperf\Database\Model\Relations\HasMany;
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
 * @property null|AdminRole[]|Collection $adminRole
 * @property null|AdminDept[]|Collection $adminDept
 * @property null|Post $post
 */
class Admin extends Model implements Authenticatable
{
    use AdminRepository;
    use SoftDeletes;
    use AdminVo;

    /** 类型：超级管理员 */
    public const int TYPE_SUPER = 1;
    /** 类型：普通管理员 */
    public const int TYPE_NORMAL = 2;
    /** 状态：启用 */
    public const int STATUS_ENABLE = 1;
    /** 状态：禁用 */
    public const int STATUS_DISABLED = 0;

    protected ?string $table = 'admin';

    protected array $fillable = ['id', 'name', 'password', 'status', 'type', 'mobile', 'email', 'last_login_ip', 'logo', 'dept_id', 'last_login_time', 'created_at', 'updated_at', 'deleted_at'];

    protected array $casts = ['id' => 'integer', 'status' => 'integer', 'type' => 'integer', 'dept_id' => 'integer', 'last_login_time' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function adminRole(): HasMany
    {
        return $this->hasMany(AdminRole::class, 'admin_id', 'id');
    }

    public function adminDept(): HasMany
    {
        return $this->hasMany(AdminDept::class, 'admin_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }
}
