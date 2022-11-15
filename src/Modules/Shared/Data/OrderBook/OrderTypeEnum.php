<?php

declare(strict_types=1);

namespace Modules\Shared\Data\OrderBook;

enum OrderTypeEnum: string
{
    case BID = 'BID';
    case ASK = 'ASK';
}
