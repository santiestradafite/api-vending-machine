<?php

declare(strict_types=1);

namespace Api\Domain\Exception;

use Shared\Common\Exception\Exception;
use Shared\Domain\StringValueObject;

final class ItemNotVendedException extends Exception
{
    public static function becauseItemIsNotFound(StringValueObject $itemName): self
    {
        return self::create(sprintf('Item <%s> is not in the vending machine', $itemName->value()));
    }

    public static function becauseItemHasNoStock(StringValueObject $itemName): self
    {
        return self::create(sprintf('Item <%s> is out of stock', $itemName->value()));
    }

    public static function becauseInsertedMoneyIsNotEnough(StringValueObject $itemName): self
    {
        return self::create(
            sprintf('Item <%s> can not be vended because the money introduced is not enough', $itemName->value())
        );
    }

    public static function becauseTheMachineHasNoChange(StringValueObject $itemName): self
    {
        return self::create(
            sprintf('Item <%s> can not be vended because the machine has no change', $itemName->value())
        );
    }
}