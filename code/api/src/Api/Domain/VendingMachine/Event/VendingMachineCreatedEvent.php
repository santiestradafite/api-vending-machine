<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Domain\Event\DomainEvent;

final class VendingMachineCreatedEvent extends DomainEvent
{
    private ItemCollection $items;
    private CoinCollection $coins;

    private function __construct(
        VendingMachineId $vendingMachineId,
        ItemCollection $items,
        CoinCollection $coins
    ) {
        parent::__construct($vendingMachineId->value());

        $this->items = $items;
        $this->coins = $coins;
    }

    public static function create(
        VendingMachineId $vendingMachineId,
        ItemCollection $items,
        CoinCollection $coins
    ): self {
        return new self($vendingMachineId, $items, $coins);
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