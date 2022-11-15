<?php

declare(strict_types=1);

namespace Modules\Shared\Data;

class MarketDto
{
    public function __construct(
        public readonly string $base,
        public readonly string $quote,
    ) {
    }
}
