<?php

declare(strict_types=1);

namespace Modules\RateFinder\Data;

class RateDto
{
    public function __construct(
        public readonly PathToPairDto $path,
        public readonly float $rate
    ) {
    }
}
