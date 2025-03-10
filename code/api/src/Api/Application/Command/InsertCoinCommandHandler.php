<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Aggregate\Coin;
use Api\Domain\VendingMachine\Aggregate\CoinId;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Shared\Domain\Command\CommandHandler;

final class InsertCoinCommandHandler implements CommandHandler
{
    public function __construct(private readonly VendingMachineRepositoryInterface $vendingMachineRepository)
    {
    }

    public function __invoke(InsertCoinCommand $command): void
    {
        $vendingMachine = $this->vendingMachineRepository->findOrFail(VendingMachineId::fromString($command->vendingMachineId()));

        $vendingMachine->insertCoin(Coin::create(CoinId::generate(), CoinValue::create($command->coinValue())));

        $this->vendingMachineRepository->save($vendingMachine);
    }
}