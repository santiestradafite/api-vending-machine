<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\VendingMachine\Aggregate\ItemId;

final class StubItemId
{
    public const DEFAULT_ID = 'c188617f-f58b-4b42-b625-27b0137911dc';
    public const OTHER_ID = '09e2dd55-6023-45ea-b1e6-1c84885a3686';

    public static function create(?string $itemId = null): ItemId
    {
        return ItemId::fromString($itemId ?? self::DEFAULT_ID);
    }

    public static function createOther(): ItemId
    {
        return ItemId::fromString(self::OTHER_ID);
    }
}
