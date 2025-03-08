<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\VendingMachine\Aggregate\Item;
use Api\Domain\VendingMachine\Aggregate\ItemId;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Shared\Domain\BoolValueObject;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

final class StubItem
{
    public const DEFAULT_NAME = 'Water';
    public const DEFAULT_PRICE = 0.75;
    public const DEFAULT_STOCK = 3;

    public static function create(
        ?ItemId $itemId = null,
        ?StringValueObject $name = null,
        ?ItemPrice $price = null,
        ?IntValueObject $stock = null
    ): Item {
        return Item::create(
            $itemId ?? StubItemId::create(),
            $name ?? new StringValueObject(self::DEFAULT_NAME),
            $price ?? ItemPrice::create(self::DEFAULT_PRICE),
            $stock ?? new IntValueObject(self::DEFAULT_STOCK)
        );
    }
}
