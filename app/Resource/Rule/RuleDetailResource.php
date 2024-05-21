<?php

namespace App\Resource\Rule;

use App\Model\Rule;
use Hyperf\Resource\Json\JsonResource;

/**
 * @mixin Rule
 */
class RuleDetailResource extends JsonResource
{
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'status' => $this->status,
            'type' => $this->type,
            'sort' => $this->sort,
            'name' => $this->name,
            'icon' => $this->icon,
            'desc' => $this->desc,
            'route' => $this->route,
            'path' => $this->path,
        ];
    }
}
