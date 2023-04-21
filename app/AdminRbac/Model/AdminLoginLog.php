<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\AdminRbac\Model\Admin\Admin;
use App\Model\Model;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $admin_id
 * @property string $province
 * @property string $city
 * @property int $last_login_time
 * @property int $last_login_ip
 */
class AdminLoginLog extends Model
{
    protected ?string $table = 'admin_login_log';

    protected array $fillable = [
        'admin_id',
        'province',
        'city',
        'last_login_time',
        'last_login_ip',
    ];

    protected array $casts = [
        'id' => 'integer',
        'admin_id' => 'integer',
        'last_login_time' => 'integer',
        'last_login_ip' => 'integer',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->select('id', 'name', 'mobile');
    }
}
