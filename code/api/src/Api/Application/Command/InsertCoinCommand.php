<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Shared\Domain\Command\Command;

final class InsertCoinCommand implements Command
{
    public function __construct(private readonly string $vendingMachineId, private readonly float $coinValue)
    {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function coinValue(): float
    {
        return $this->coinValue;
    }
}