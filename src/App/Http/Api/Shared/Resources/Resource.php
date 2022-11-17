<?php

declare(strict_types=1);

namespace App\Http\Api\Shared\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    /**
     * @var string|null
     */
    public static $wrap = 'result';

    public $with = ['error' => null];
}
