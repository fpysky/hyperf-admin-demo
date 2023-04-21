<?php

declare(strict_types=1);

namespace App\Model\BusinessCircle\Traits;

use App\Model\Area\Area;
use Hyperf\Database\Model\Relations\HasOne;

trait BusinessCircleRelationship
{
    public function area(): HasOne
    {
        return $this->hasOne(Area::class,'id','area_id');
    }
}
