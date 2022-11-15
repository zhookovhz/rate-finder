<?php

declare(strict_types=1);

namespace Modules\RateFinder\Gateways;

use Illuminate\Support\Collection;
use Modules\Shared\Data\MarketDto;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;

interface ExchangeGatewayInterface
{
    /** @return Collection<MarketDto> */
    public function getMarkets(): Collection;

    /**
     * @param GetOrderBookDto $dto
     * @return Collection
     */
    public function getOrderBook(GetOrderBookDto $dto): Collection;
}
