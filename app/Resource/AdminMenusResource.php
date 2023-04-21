<?php

namespace App\Resource;

use App\AdminRbac\Model\Rule\Rule;
use Hyperf\Database\Model\Collection;
use Hyperf\Resource\Json\JsonResource;

class AdminMenusResource extends JsonResource
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
            'name' => $this->name,
            'icon' => $this->icon,
            'path' => $this->path,
            'route' => $this->route,
            'sort' => $this->order,
            'parentId' => $this->parent_id,
            'remark' => $this->desc,
            'status' => $this->status,
            'type' => $this->type,
            'createTime' => $this->getFormattedCreatedAt(),
            'updateTime' => $this->getFormattedUpdatedAt(),
            'deleteTime' => $this->getFormattedDeletedAt(),
            'children' => $this->children instanceof Collection?AdminMenusChildResource::collection($this->children):[],
        ];
    }
}
