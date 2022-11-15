<?php

declare(strict_types=1);

namespace Modules\Shared\Data\OrderBook;

class OrderDto
{
    public function __construct(
        public readonly float $price,
        public readonly float $amount,
    ) {
    }
}
