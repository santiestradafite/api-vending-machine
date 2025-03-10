<?php

declare(strict_types=1);

namespace Api\Application\Query;

use Shared\Domain\Query\Query;

final class GetVendingMachineQuery implements Query
{
    public function __construct(private readonly string $vendingMachineId)
    {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }
}