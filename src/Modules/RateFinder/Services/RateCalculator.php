<?php

declare(strict_types=1);

namespace Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Data\PathToPairDto;
use Modules\RateFinder\Data\RateDto;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;

class RateCalculator
{
    public function __construct(private readonly ExchangeGatewayInterface $exchangeGateway)
    {
    }

    /**
     * @param Collection<PathToPairDto> $paths
     * @param FindRateDto $dto
     * @return Collection<RateDto>
     */
    public function calculate(Collection $paths, FindRateDto $dto): Collection
    {
        $rates = [];
        foreach ($paths as $path) {
            $rate = $this->findRate($path, $dto);
            if (!is_null($rate)) {
                $rates[] = $rate;
            }
        }

        return new Collection($rates);
    }

    private function findRate(PathToPairDto $path, FindRateDto $dto): ?RateDto
    {
        $price = 0;
        $foundInOrderBook = 0;
        $amount = $dto->amount;

        foreach ($path->pairs as $pair)
        {
            $orders = $this->exchangeGateway->getOrderBook(new GetOrderBookDto(
                $pair->from,
                $pair->to,
                $pair->direction
            ));

            foreach ($orders as $order) {
                if ($order->amount >= $amount) {
                    $price = $this->calcPrice($price, $order->price, $pair->direction);
                    $amount = $this->calcAmount($amount, $price, $pair->direction);
                    $foundInOrderBook++;
                    break;
                }
            }
        }

        if ($price === 0 || $foundInOrderBook !== $path->pairs->count()) {
            return null;
        }

        return new RateDto($path, $price);
    }

    private function calcPrice(float $currentPrice, float $orderPrice, OrderTypeEnum $direction): float
    {
        if ($currentPrice == 0) {
            $currentPrice = 1;
        }

        if ($direction === OrderTypeEnum::ASK) {
            $currentPrice *= $orderPrice;
        } else {
            $currentPrice /= $orderPrice;
        }

        return $currentPrice;
    }

    private function calcAmount(float $currentAmount, float $currentPrice, OrderTypeEnum $direction): float
    {
        if ($direction === OrderTypeEnum::ASK) {
            $currentAmount *= $currentPrice;
        } else {
            $currentAmount /= $currentPrice;
        }

        return $currentAmount;
    }
}
