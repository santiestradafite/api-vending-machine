<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\Coin;
use Api\Domain\Aggregate\CoinValue;
use Api\Domain\Aggregate\ItemPrice;
use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubCoinId;

final class ItemPriceTest extends TestCase
{
    /** @dataProvider allowedValues */
    public function test_it_can_create_an_item_price(float $allowedValue): void
    {
        $sut = ItemPrice::create($allowedValue);

        self::assertEquals($allowedValue, $sut->value());
    }

    public function allowedValues(): array
    {
        return [
            [0.05],
            [0.10],
            [0.25],
            [1.00],
            [0.50],
            [0.55],
            [0.6],
            [0.75],
            [106.75],
            [200.00]
        ];
    }

    public function test_it_fails_when_price_value_is_not_allowed(): void
    {
        $this->expectException(AssertionFailedException::class);

        CoinValue::create(1.72);
    }
}