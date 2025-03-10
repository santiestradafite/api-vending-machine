<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Aggregate\ItemId;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Shared\Domain\Command\CommandHandler;

final class VendItemCommandHandler implements CommandHandler
{
    public function __construct(private readonly VendingMachineRepositoryInterface $vendingMachineRepository)
    {
    }

    public function __invoke(VendItemCommand $command): void
    {
        $vendingMachine = $this->vendingMachineRepository->findOrFail(VendingMachineId::fromString($command->vendingMachineId()));

        $vendingMachine->vendItem(ItemId::fromString($command->itemId()));

        $this->vendingMachineRepository->save($vendingMachine);
    }
}