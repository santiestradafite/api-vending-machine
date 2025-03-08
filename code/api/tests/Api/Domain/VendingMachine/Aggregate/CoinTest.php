<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Aggregate;

use Api\Domain\VendingMachine\Aggregate\Coin;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubCoinId;

final class CoinTest extends TestCase
{
    public function test_it_can_create_a_coin(): void
    {
        $id = StubCoinId::create();
        $value = CoinValue::create(StubCoin::DEFAULT_VALUE);

        $sut = Coin::create($id, $value);

        self::assertEquals($id, $sut->id());
        self::assertEquals($value, $sut->value());
        self::assertFalse($sut->isInserted()->value());
        self::assertFalse($sut->isReturned()->value());
    }

    public function test_it_can_insert_a_coin(): void
    {
        $sut = StubCoin::create();
        $sut->insert();

        self::assertTrue($sut->isInserted()->value());
        self::assertFalse($sut->isReturned()->value());
    }

    public function test_it_can_return_a_coin(): void
    {
        $sut = StubCoin::create();
        $sut->return();

        self::assertFalse($sut->isInserted()->value());
        self::assertTrue($sut->isReturned()->value());
    }

    public function test_it_can_collect_a_coin(): void
    {
        $sut = StubCoin::create();
        $sut->insert();
        $sut->return();
        $sut->collect();

        self::assertFalse($sut->isInserted()->value());
        self::assertFalse($sut->isReturned()->value());
    }
}