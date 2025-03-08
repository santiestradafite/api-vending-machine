<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\FloatType;
use Shared\Domain\FloatValueObject;

final class DoctrineFloatValueObjectType extends FloatType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getFloatDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?float
    {
        /** @var FloatValueObject $value */
        if ($value === null) {
            return null;
        }

        return $value->value();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?FloatValueObject
    {
        if ($value === null) {
            return null;
        }

        return new FloatValueObject((float) $value);
    }

    public function getName(): string
    {
        return 'float_value_object';
    }
}
