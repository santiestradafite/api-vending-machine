<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Shared\Domain\StringValueObject;

final class DoctrineStringValueObjectType extends JsonType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        /** @var StringValueObject $value */
        if ($value === null) {
            return null;
        }

        return $value->value();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?StringValueObject
    {
        if ($value === null) {
            return null;
        }

        return new StringValueObject($value);
    }

    public function getName(): string
    {
        return 'string_value_object';
    }
}
