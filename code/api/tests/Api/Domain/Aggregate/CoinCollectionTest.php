<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\CoinCollection;
use Api\Domain\Aggregate\CoinValue;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;

final class CoinCollectionTest extends TestCase
{
    public function test_it_can_sort_by_value(): void
    {
        $sut = CoinCollection::create(
            [
                StubCoin::create(),
                StubCoin::createRandomWithValue(CoinValue::create(1)),
                StubCoin::createRandomWithValue(CoinValue::create(0.25))
            ]
        );
        $sortedCoins = $sut->sortByValue();

        self::assertEquals(CoinValue::create(1), $sortedCoins->first()->value());
        self::assertEquals(CoinValue::create(0.25), $sortedCoins->next()->value());
        self::assertEquals(CoinValue::create(0.05), $sortedCoins->next()->value());
    }
}