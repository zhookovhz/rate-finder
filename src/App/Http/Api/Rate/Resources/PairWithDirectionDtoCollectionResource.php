<?php

declare(strict_types=1);

namespace App\Http\Api\Rate\Resources;

use App\Http\Api\Shared\Resources\ResourceCollection;

class PairWithDirectionDtoCollectionResource extends ResourceCollection
{
    public $collects = PairWithDirectionDtoResource::class;
}
