<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Shared\Domain\Command\Command;

final class VendItemCommand implements Command
{
    public function __construct(private readonly string $vendingMachineId, private readonly string $itemId)
    {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function itemId(): string
    {
        return $this->itemId;
    }
}