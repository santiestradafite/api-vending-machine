<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\Persistence;

use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;

final class InMemoryVendingMachineRepository extends InMemoryRepository implements VendingMachineRepositoryInterface
{
    public function save(VendingMachine $vendingMachine): void
    {
        $this->doSave($vendingMachine);
    }

    public function findOrFail(VendingMachineId $vendingMachineId): VendingMachine
    {
        return $this->doFindOrFail($vendingMachineId);
    }

    protected static function entityClassName(): string
    {
        return VendingMachine::class;
    }
}
