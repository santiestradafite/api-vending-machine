<?php

declare(strict_types=1);

namespace Api\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Api\Domain\VendingMachine\Aggregate\CoinId;
use Shared\Infrastructure\Persistence\Doctrine\MySQL\Type\DoctrineUuidType;

final class DoctrineCoinIdType extends DoctrineUuidType
{
    protected function specificUuidType(): string
    {
        return CoinId::class;
    }

    public function getName(): string
    {
        return 'coin_id';
    }
}
