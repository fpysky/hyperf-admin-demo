<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Rule\Rule;
use Hyperf\Resource\Json\JsonResource;

class RuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Rule $this */
        return [
            'id' => $this->id,
            'parentId' => $this->parent_id,
            'status' => $this->status,
            'type' => $this->type,
            'typeZh' => $this->getTypeZh(),
            'sort' => $this->sort,
            'name' => $this->name,
            'icon' => $this->icon,
            'route' => $this->route,
            'path' => $this->path,
            'children' => self::collection($this->children),
        ];
    }
}
