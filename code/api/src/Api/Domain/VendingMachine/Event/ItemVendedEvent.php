<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Aggregate\ItemId;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Domain\Event\DomainEvent;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

final class ItemVendedEvent extends DomainEvent
{
    private VendingMachineId $vendingMachineId;
    private StringValueObject $name;
    private ItemPrice $price;
    private IntValueObject $stock;

    private function __construct(
        ItemId $itemId,
        VendingMachineId $vendingMachineId,
        StringValueObject $name,
        ItemPrice $price,
        IntValueObject $stock
    ) {
        parent::__construct($itemId->value());

        $this->vendingMachineId = $vendingMachineId;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public static function create(
        ItemId $itemId,
        VendingMachineId $vendingMachineId,
        StringValueObject $name,
        ItemPrice $price,
        IntValueObject $stock
    ): self {
        return new self($itemId, $vendingMachineId, $name, $price, $stock);
    }

    public function vendingMachineId(): VendingMachineId
    {
        return $this->vendingMachineId;
    }

    public function name(): StringValueObject
    {
        return $this->name;
    }

    public function price(): ItemPrice
    {
        return $this->price;
    }

    public function stock(): IntValueObject
    {
        return $this->stock;
    }
}