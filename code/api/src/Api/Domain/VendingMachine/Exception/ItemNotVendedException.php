<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Exception;

use Api\Domain\VendingMachine\Aggregate\ItemId;
use Shared\Common\Exception\Exception;

final class ItemNotVendedException extends Exception
{
    public static function becauseItemIsNotFound(ItemId $itemId): self
    {
        return self::create(sprintf('Item with id <%s> is not in the vending machine', $itemId->value()));
    }

    public static function becauseItemHasNoStock(ItemId $itemId): self
    {
        return self::create(sprintf('Item with id <%s> is out of stock', $itemId->value()));
    }

    public static function becauseInsertedMoneyIsNotEnough(ItemId $itemId): self
    {
        return self::create(
            sprintf('Item with id <%s> can not be vended because the money introduced is not enough', $itemId->value())
        );
    }

    public static function becauseTheMachineHasNoChange(ItemId $itemId): self
    {
        return self::create(
            sprintf('Item with id <%s> can not be vended because the machine has no change', $itemId->value())
        );
    }
}