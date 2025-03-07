<?php

declare(strict_types=1);

namespace Api\Domain\Event;

use Api\Domain\Aggregate\CoinCollection;
use Api\Domain\Aggregate\ItemCollection;
use Api\Domain\Aggregate\VendingMachineId;
use Shared\Domain\Event\DomainEvent;

final class VendingMachineCreatedEvent extends DomainEvent
{
    private ItemCollection $items;
    private CoinCollection $availableCoins;

    private function __construct(
        VendingMachineId $vendingMachineId,
        ItemCollection $items,
        CoinCollection $availableCoins
    ) {
        parent::__construct($vendingMachineId->value());

        $this->items = $items;
        $this->availableCoins = $availableCoins;
    }

    public static function create(
        VendingMachineId $vendingMachineId,
        ItemCollection $items,
        CoinCollection $availableCoins
    ): self {
        return new self($vendingMachineId, $items, $availableCoins);
    }

    public function items(): ItemCollection
    {
        return $this->items;
    }

    public function availableCoins(): CoinCollection
    {
        return $this->availableCoins;
    }
}