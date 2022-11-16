<?php

declare(strict_types=1);

namespace Modules\RateFinder\Gateways;

use Illuminate\Support\Collection;
use Modules\Shared\Data\PairDto;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;

interface ExchangeGatewayInterface
{
    /** @return Collection<PairDto> */
    public function getMarkets(): Collection;

    /**
     * @param GetOrderBookDto $dto
     * @return Collection
     */
    public function getOrderBook(GetOrderBookDto $dto): Collection;
}
