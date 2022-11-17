<?php

declare(strict_types=1);

namespace Tests\Feature\Infractructure\Gateways\Binance;

use ccxt\binance;
use Infrastructure\Gateways\Binance\BinanceGateway;
use Infrastructure\Gateways\Binance\Exceptions\UnknownOrderTypeException;
use Modules\Shared\Data\OrderBook\GetOrderBookDto;
use Modules\Shared\Data\OrderBook\OrderTypeEnum;
use Tests\TestCase;

class BinanceGatewayTest extends TestCase
{
    /** @dataProvider getMarketsDataProvider */
    public function testGetMarkets(array $markets): void
    {
        $return = [];
        foreach ($markets as $market) {
            $return[] = [
                'base' => $market['from'],
                'quote' => $market['to'],
            ];
        }

        $binance = $this->createMock(binance::class);
        $binance->expects($this->once())->method('load_markets')->willReturn($return);
        $this->app->instance(binance::class, $binance);

        /** @var BinanceGateway $gateway */
        $gateway = $this->app->make(BinanceGateway::class);
        $result = $gateway->getMarkets();

        foreach ($result as $key => $market) {
            $this->assertEquals($markets[$key]['from'], $market->from);
            $this->assertEquals($markets[$key]['to'], $market->to);
        }
    }

    public function getMarketsDataProvider(): array
    {
        return [
            [
                'markets' => [
                    ['from' => 'ETH', 'to' => 'SOL'],
                    ['from' => 'BTC', 'to' => 'ETH'],
                    ['from' => 'XRP', 'to' => 'USDT'],
                    ['from' => 'BUSD', 'to' => 'NEAR'],
                    ['from' => 'APT', 'to' => 'USN'],
                ],
            ],
        ];
    }

    /**
     * @throws UnknownOrderTypeException
     * @dataProvider findInOrderBookDataProvider
     */
    public function testFindInOrderBook(OrderTypeEnum $type, array $orders): void
    {
        $typeKey = strtolower($type->value) . 's';

        $binance = $this->createMock(binance::class);
        $binance->expects($this->once())->method('fetch_order_book')->willReturn($orders);
        $this->app->instance(binance::class, $binance);

        /** @var BinanceGateway $gateway */
        $gateway = $this->app->make(BinanceGateway::class);
        $result = $gateway->getOrderBook(new GetOrderBookDto(
            'ETH', 'BTC', $type
        ));

        foreach ($result as $key => $order) {
            $this->assertEquals($orders[$typeKey][$key][0], $order->price);
            $this->assertEquals($orders[$typeKey][$key][1], $order->amount);
        }
    }

    public function findInOrderBookDataProvider(): array
    {
        $bids = [
            [0.123, 0.09],
            [0.122, 0.076],
            [0.111, 0.032],
        ];

        $asks = [
            [0.124, 0.087],
            [0.134, 0.065],
            [0.137, 0.034],
        ];

        return [
            'bids' => [
                'type' => OrderTypeEnum::BID,
                'orders' => [
                    'bids' => $bids,
                    'asks' => $asks,
                ],
            ],
            'asks' => [
                'type' => OrderTypeEnum::ASK,
                'orders' => [
                    'bids' => $bids,
                    'asks' => $asks,
                ],
            ],
        ];
    }
}
