<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Shared\Domain\Command\CommandHandler;

final class CollectItemAndCoinsCommandHandler implements CommandHandler
{
    public function __construct(private readonly VendingMachineRepositoryInterface $vendingMachineRepository)
    {
    }

    public function __invoke(CollectItemAndCoinsCommand $command): void
    {
        $vendingMachine = $this->vendingMachineRepository->findOrFail(VendingMachineId::fromString($command->vendingMachineId()));

        $vendingMachine->collectVendedItemAndReturnedCoins();

        $this->vendingMachineRepository->save($vendingMachine);
    }
}