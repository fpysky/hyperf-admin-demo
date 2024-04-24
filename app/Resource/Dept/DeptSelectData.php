<?php

declare(strict_types=1);

namespace App\Resource\Dept;

use App\Model\Dept\Dept;
use Hyperf\Resource\Json\JsonResource;

class DeptSelectData extends JsonResource
{
    public function toArray(): array
    {
        /** @var Dept $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'children' => DeptSelectDataChild::collection($this->enabledChildren),
        ];
    }
}
