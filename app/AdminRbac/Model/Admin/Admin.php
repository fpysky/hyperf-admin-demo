<?php

declare(strict_types=1);

namespace App\AdminRbac\Model\Admin;

use App\AdminRbac\Model\Admin\Traits\AdminRelationship;
use App\AdminRbac\Model\Admin\Traits\AdminRepository;
use App\AdminRbac\Model\Dept;
use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Collection;
use Hyperf\Database\Model\SoftDeletes;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $password
 * @property int $status
 * @property int $type
 * @property string $mobile
 * @property string $email
 * @property string $last_login_ip
 * @property string $logo
 * @property int $dept_id
 * @property int $post_id
 * @property int $last_login_time
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property mixed $deleted_at
 * @property Collection $adminRole
 * @property Dept $dept
 */
class Admin extends Model implements Authenticatable
{
    use AdminRelationship;
    use AdminRepository;
    use SoftDeletes;

    protected ?string $table = 'admin';

    protected array $fillable = [
        'name',
        'password',
        'status',
        'type',
        'mobile',
        'email',
        'last_login_ip',
        'logo',
        'dept_id',
        'post_id',
        'last_login_time',
    ];

    protected array $casts = [
        'id' => 'integer',
        'status' => 'integer',
        'type' => 'integer',
        'dept_id' => 'integer',
        'post_id' => 'integer',
        'last_login_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getId(): int
    {
        return $this->id;
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::query()->findOrFail((int) $key);
    }
}
