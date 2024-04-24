<?php

declare(strict_types=1);

namespace App\Model\Admin;

use App\Model\Origin\AdminLoginLog as Base;
use Hyperf\Database\Model\Relations\BelongsTo;

class AdminLoginLog extends Base
{
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')
            ->select('id', 'name', 'mobile');
    }
}
