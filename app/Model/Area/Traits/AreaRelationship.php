<?php

declare(strict_types=1);

namespace App\Model\Area\Traits;

use Hyperf\Database\Model\Relations\HasOne;

trait AreaRelationship
{
    public function parent(): HasOne
    {
        return $this->hasOne(self::class,'id','pid');
    }
}
