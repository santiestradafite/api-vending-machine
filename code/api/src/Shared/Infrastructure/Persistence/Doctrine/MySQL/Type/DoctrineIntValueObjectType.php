<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use Shared\Domain\IntValueObject;

final class DoctrineIntValueObjectType extends IntegerType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getIntegerTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        /** @var IntValueObject $value */
        if ($value === null) {
            return null;
        }

        return $value->value();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?IntValueObject
    {
        if ($value === null) {
            return null;
        }

        return new IntValueObject((int) $value);
    }

    public function getName(): string
    {
        return 'int_value_object';
    }
}
