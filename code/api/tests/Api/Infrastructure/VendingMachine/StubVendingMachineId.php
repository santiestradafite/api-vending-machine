<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\Aggregate\VendingMachineId;

final class StubVendingMachineId
{
    public const DEFAULT_ID = '6386a751-5c2a-4200-879d-85634862214e';

    public static function create(?string $vendingMachineId = null): VendingMachineId
    {
        return VendingMachineId::fromString($vendingMachineId ?? self::DEFAULT_ID);
    }
}
