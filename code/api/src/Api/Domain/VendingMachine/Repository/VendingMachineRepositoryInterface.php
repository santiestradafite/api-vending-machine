<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Repository;

use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;

interface VendingMachineRepositoryInterface
{
    public function save(VendingMachine $vendingMachine): void;
    public function findOrFail(VendingMachineId $vendingMachineId): VendingMachine;
}
