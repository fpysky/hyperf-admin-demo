<?php

namespace App\Resource;

use App\Model\Merchant\Tag\MerchantTag;
use Hyperf\Resource\Json\JsonResource;

class MerchantTagInfo extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var MerchantTag $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'merchantNum' => 0, // todo::假数据
            'createdAt' => $this->getFormattedCreatedAt('Y-m-d H:i'),
        ];
    }
}
