<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\VendingMachine\Aggregate\CoinId;

final class StubCoinId
{
    public const DEFAULT_ID = '056ea3db-a661-4bc3-8b40-fcdea0ecbe96';
    public const OTHER_ID = 'f6a5ce3b-860d-4beb-8a9d-169ddf9aaa95';

    public static function create(?string $coinId = null): CoinId
    {
        return CoinId::fromString($coinId ?? self::DEFAULT_ID);
    }

    public static function createOther(): CoinId
    {
        return CoinId::fromString(self::OTHER_ID);
    }
}
