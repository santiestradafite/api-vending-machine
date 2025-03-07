<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\Aggregate\Coin;
use Api\Domain\Aggregate\CoinId;
use Api\Domain\Aggregate\CoinValue;

final class StubCoin
{
    public const DEFAULT_VALUE = 0.05;

    public static function create(
        ?CoinId $coinId = null,
        ?CoinValue $value = null
    ): Coin {
        return Coin::create(
            $coinId ?? StubCoinId::create(),
            $value ?? CoinValue::create(self::DEFAULT_VALUE)
        );
    }

    public static function createRandomWithValue(CoinValue $value): Coin
    {
        return Coin::create(CoinId::generate(), $value);
    }
}
