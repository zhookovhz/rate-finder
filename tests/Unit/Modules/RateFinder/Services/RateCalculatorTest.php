<?php

declare(strict_types=1);

namespace Tests\Unit\Modules\RateFinder\Services;

use Illuminate\Support\Collection;
use Modules\RateFinder\Data\FindRateDto;
use Modules\RateFinder\Data\PairWithDirectionDto;
use Modules\RateFinder\Data\PathToPairDto;
use Modules\RateFinder\Gateways\ExchangeGatewayInterface;
use Modules\RateFinder\Services\RateCalculator;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;
use Modules\Shared\Data\OrderBook\OrderDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Tests\TestCase;

class RateCalculatorTest extends TestCase
{
    public function testCalculate()
    {
        $from = 'ETH';
        $to = 'BTC';
        $amount = 0.45;

        $paths = [
            new PathToPairDto(
                new Collection([
                    new PairWithDirectionDto(
                        'ETH', 'XRP', OrderTypeEnum::ASK
                    ),
                    new PairWithDirectionDto(
                        'XRP', 'BTC', OrderTypeEnum::ASK
                    ),
                ])
            ),
            new PathToPairDto(
                new Collection([
                    new PairWithDirectionDto(
                        'ETH', 'SOL', OrderTypeEnum::ASK
                    ),
                    new PairWithDirectionDto(
                        'BTC', 'SOL', OrderTypeEnum::BID
                    ),
                ])
            ),
        ];
        $paths = new Collection($paths);
        $findRateDto = new FindRateDto($from, $to, $amount);

        $gatewayReturnETH_XRP = new Collection([
            new OrderDto(0.2, 0.3),
            new OrderDto(0.1, 0.46),
            new OrderDto(0.09, 0.54),
        ]);
        $gatewayReturnXRP_BTC = new Collection([
            new OrderDto(0.2, 0.03),
            new OrderDto(0.1, 0.046),
            new OrderDto(0.09, 0.054),
        ]);
        $gatewayReturnETH_SOL = new Collection([
            new OrderDto(0.2, 0.3),
            new OrderDto(0.1, 0.46),
            new OrderDto(0.09, 0.54),
        ]);
        $gatewayReturnBTC_SOL = new Collection([
            new OrderDto(0.2, 0.03),
            new OrderDto(0.1, 0.046),
            new OrderDto(0.09, 0.054),
        ]);

        $gateway = $this->createMock(ExchangeGatewayInterface::class);
        $gateway->expects($this->any())->method('getOrderBook')->willReturnCallback(function (
            GetOrderBookDto $dto
        ) use ($gatewayReturnETH_XRP, $gatewayReturnXRP_BTC, $gatewayReturnETH_SOL, $gatewayReturnBTC_SOL) {
            if ($dto->from === 'ETH' && $dto->to === 'XRP' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnETH_XRP;
            }
            if ($dto->from === 'XRP' && $dto->to === 'BTC' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnXRP_BTC;
            }
            if ($dto->from === 'ETH' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::ASK) {
                return $gatewayReturnETH_SOL;
            }
            if ($dto->from === 'BTC' && $dto->to === 'SOL' && $dto->orderTypeEnum === OrderTypeEnum::BID) {
                return $gatewayReturnBTC_SOL;
            }
            return null;
        });

        $service = new RateCalculator($gateway);

        $result = $service->calculate($paths, $findRateDto);

        $this->assertEquals(0.01, round($result->get(0)->rate, 2));
        $this->assertEquals(1.0, $result->get(1)->rate);
    }
}
