<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Shared\Domain\BoolValueObject;

final class DoctrineBoolValueObjectType extends JsonType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBooleanTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?int
    {
        /** @var BoolValueObject $value */
        if ($value === null) {
            return null;
        }

        return $value->toNumber();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BoolValueObject
    {
        if ($value === null) {
            return null;
        }

        return new BoolValueObject((bool) $value);
    }

    public function getName(): string
    {
        return 'bool_value_object';
    }
}
