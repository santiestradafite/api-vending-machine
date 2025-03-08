<?php

declare(strict_types=1);

namespace Api\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Api\Domain\VendingMachine\Aggregate\ItemId;
use Shared\Infrastructure\Persistence\Doctrine\MySQL\Type\DoctrineUuidType;

final class DoctrineItemIdType extends DoctrineUuidType
{
    protected function specificUuidType(): string
    {
        return ItemId::class;
    }

    public function getName(): string
    {
        return 'item_id';
    }
}
