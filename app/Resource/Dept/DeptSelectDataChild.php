<?php

namespace App\Resource\Dept;

use App\Model\Dept;
use Hyperf\Resource\Json\JsonResource;

class DeptSelectDataChild extends JsonResource
{
    public function toArray(): array
    {
        /** @var Dept $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
