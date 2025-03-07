<?php

declare(strict_types=1);

namespace Api\Domain\Repository;

use Api\Domain\Aggregate\VendingMachine;
use Api\Domain\Aggregate\VendingMachineId;

interface VendingMachineRepositoryInterface
{
    public function save(VendingMachine $vendingMachine): void;
    public function findOrFail(VendingMachineId $vendingMachineId): VendingMachine;
}
