<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;

final class CoinCollectionTest extends TestCase
{
    public function test_it_can_sort_by_value(): void
    {
        $coin = StubCoin::create();
        $otherCoin = StubCoin::createRandomWithValue(CoinValue::create(1));
        $anotherCoin = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $sut = CoinCollection::create([$coin, $otherCoin, $anotherCoin]);

        $sortedCoins = $sut->sortByValue();

        self::assertEquals($otherCoin, $sortedCoins->first());
        self::assertEquals($anotherCoin, $sortedCoins->next());
        self::assertEquals($coin, $sortedCoins->next());
    }

    public function test_it_can_filter_inserted_coins(): void
    {
        $coin = StubCoin::create();
        $otherCoin = StubCoin::createRandomWithValue(CoinValue::create(1));
        $anotherCoin = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $anotherCoin->insert();
        $sut = CoinCollection::create([$coin, $otherCoin, $anotherCoin]);

        $insertedCoins = $sut->filterInserted();

        self::assertCount(1, $insertedCoins);
        self::assertEquals($anotherCoin, $insertedCoins->firstOrFail());
    }

    public function test_it_can_filter_returned_coins(): void
    {
        $coin = StubCoin::create();
        $otherCoin = StubCoin::createRandomWithValue(CoinValue::create(1));
        $anotherCoin = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $anotherCoin->return();
        $sut = CoinCollection::create([$coin, $otherCoin, $anotherCoin]);

        $returnedCoins = $sut->filterReturned();

        self::assertCount(1, $returnedCoins);
        self::assertEquals($anotherCoin, $returnedCoins->firstOrFail());
    }
}