<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Admin;
use Hyperf\Resource\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(): array
    {
        /** @var Admin $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'lastLoginIp' => $this->last_login_ip,
            'logo' => $this->logo,
            'roleIds' => $this->roleIds(),
            'lastLoginTime' => $this->getFormattedDateTime($this->last_login_time),
            'createdAt' => $this->getFormattedCreatedAt(),
            'updatedAt' => $this->getFormattedUpdatedAt(),
        ];
    }
}
