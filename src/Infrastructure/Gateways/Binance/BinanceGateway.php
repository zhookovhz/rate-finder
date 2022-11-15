<?php

declare(strict_types=1);

namespace Infrastructure\Gateways\Binance;

use ccxt\binance;
use Illuminate\Support\Collection;
use Infrastructure\Gateways\Binance\Exceptions\UnknownOrderTypeException;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
use Modules\Shared\Data\MarketDto;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;
use Modules\Shared\Data\OrderBook\OrderDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;

class BinanceGateway implements ExchangeGatewayInterface
{
    public function __construct(private readonly binance $binance)
    {
    }

    /** @return Collection<MarketDto> */
    public function getMarkets(): Collection
    {
        $markets = $this->binance->load_markets();

        $output = [];
        foreach ($markets as $market) {
            $output[] = new MarketDto(
                $market['base'],
                $market['quote']
            );
        }

        return new Collection($output);
    }

    /**
     * @param GetOrderBookDto $dto
     * @return Collection<OrderDto>
     * @throws UnknownOrderTypeException
     */
    public function getOrderBook(GetOrderBookDto $dto): Collection
    {
        $pair = $this->makePair($dto->from, $dto->to);
        $orderBook = $this->binance->fetch_order_book($pair);

        if ($dto->orderTypeEnum === OrderTypeEnum::ASK) {
            $orders = $orderBook['asks'];
        } elseif ($dto->orderTypeEnum === OrderTypeEnum::BID) {
            $orders = $orderBook['bids'];
        } else {
            throw new UnknownOrderTypeException();
        }

        $output = [];
        foreach ($orders as $order) {
            $output[] = new OrderDto(
                $order[0],
                $order[1],
            );
        }

        return new Collection($output);
    }

    private function makePair(string $from, string $to): string
    {
        return $from . '/' . $to;
    }
}
