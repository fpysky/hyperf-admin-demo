<?php

declare(strict_types=1);

namespace App\AdminRbac\Model;

use App\AdminRbac\Model\Admin\Admin;
use App\Model\Model;
use Carbon\Carbon;
use Hyperf\Database\Model\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $admin_id
 * @property int $ip
 * @property string $server
 * @property string $method
 * @property string $url
 * @property string $params
 * @property Carbon $created_at
 */
class AdminVisitLog extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_visit_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'admin_id',
        'ip',
        'server',
        'method',
        'url',
        'params',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'admin_id' => 'integer', 'ip' => 'integer', 'created_at' => 'datetime'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->select('id', 'name', 'mobile');
    }
}
