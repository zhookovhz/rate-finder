<?php

declare(strict_types=1);

namespace Modules\Shared\Data\OrderBook;

class GetOrderBookDto
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly OrderTypeEnum $orderTypeEnum
    ) {
    }
}
