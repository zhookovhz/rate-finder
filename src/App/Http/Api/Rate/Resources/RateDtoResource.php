<?php

declare(strict_types=1);

namespace App\Http\Api\Rate\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RateFinder\Data\RateDto;

/** @property RateDto $resource */
class RateDtoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'path' => new PairWithDirectionDtoCollectionResource($this->resource->path->pairs),
            'rate' => $this->resource->rate
        ];
    }
}
