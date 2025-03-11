<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Aggregate\CoinId;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Domain\Event\DomainEvent;

final class CoinInsertedEvent extends DomainEvent
{
    private VendingMachineId $vendingMachineId;
    private CoinValue $value;

    private function __construct(CoinId $coinId, VendingMachineId $vendingMachineId, CoinValue $value)
    {
        parent::__construct($coinId->value());

        $this->vendingMachineId = $vendingMachineId;
        $this->value = $value;
    }

    public static function create(CoinId $coinId, VendingMachineId $vendingMachineId, CoinValue $value): self
    {
        return new self($coinId, $vendingMachineId, $value);
    }

    public function vendingMachineId(): VendingMachineId
    {
        return $this->vendingMachineId;
    }

    public function value(): CoinValue
    {
        return $this->value;
    }
}