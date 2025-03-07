<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\Coin;
use Api\Domain\Aggregate\CoinValue;
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
    }
}