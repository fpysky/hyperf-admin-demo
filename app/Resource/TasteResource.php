<?php

declare(strict_types=1);

namespace App\Resource;

use App\Model\Taste;
use Hyperf\Resource\Json\JsonResource;

class TasteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        /** @var Taste $this */
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'coverUrl' => $this->cover_url,
            'merchantNumStr' => '1.3万', // todo::假数据
            'status' => $this->status,
        ];
    }
}
