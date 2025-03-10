<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Shared\Domain\Command\Command;

final class SetupVendingMachineCommand implements Command
{
    public function __construct(private readonly string $vendingMachineId)
    {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }
}