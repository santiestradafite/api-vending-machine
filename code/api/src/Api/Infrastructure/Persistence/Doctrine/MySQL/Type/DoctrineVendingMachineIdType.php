<?php

declare(strict_types=1);

namespace Api\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Infrastructure\Persistence\Doctrine\MySQL\Type\DoctrineUuidType;

final class DoctrineVendingMachineIdType extends DoctrineUuidType
{
    protected function specificUuidType(): string
    {
        return VendingMachineId::class;
    }

    public function getName(): string
    {
        return 'vending_machine_id';
    }
}
