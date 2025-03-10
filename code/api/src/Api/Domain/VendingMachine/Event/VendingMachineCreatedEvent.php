<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Domain\Event\DomainEvent;
use Shared\Domain\StringValueObject;

final class VendingMachineCreatedEvent extends DomainEvent
{
    private StringValueObject $name;
    private ItemCollection $items;
    private CoinCollection $coins;

    private function __construct(
        VendingMachineId $vendingMachineId,
        StringValueObject $name,
        ItemCollection $items,
        CoinCollection $coins
    ) {
        parent::__construct($vendingMachineId->value());

        $this->name = $name;
        $this->items = $items;
        $this->coins = $coins;
    }

    public static function create(
        VendingMachineId $vendingMachineId,
        StringValueObject $name,
        ItemCollection $items,
        CoinCollection $coins
    ): self {
        return new self($vendingMachineId, $name, $items, $coins);
    }

    public function name(): StringValueObject
    {
        return $this->name;
    }

    public function items(): ItemCollection
    {
        return $this->items;
    }

    public function coins(): CoinCollection
    {
        return $this->coins;
    }
}