<?php

declare(strict_types=1);

namespace Modules\RateFinder\Data;

use Modules\Shared\Data\OrderBook\OrderTypeEnum;

class PairWithDirectionDto
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly OrderTypeEnum $direction,
    ) {
    }
}
