<?php

declare(strict_types=1);

namespace App\Extend\Resource\Json;

use App\Extend\Resource\Response\PaginatedResponse;
use Hyperf\Resource\Json\ResourceCollection as Base;
use Psr\Http\Message\ResponseInterface;

class ResourceCollection extends Base
{
    public function toResponse(): ResponseInterface
    {
        if ($this->isPaginatorResource($this->resource)) {
            return (new PaginatedResponse($this))->toResponse();
        }

        return parent::toResponse();
    }

    public function toArrResponse(): array
    {
        if ($this->isPaginatorResource($this->resource)) {
            return (new PaginatedResponse($this))->toArrResponse();
        }

        return [];
    }
}
