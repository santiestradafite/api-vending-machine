<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Doctrine\MySQL\Type;

use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Shared\Domain\Uuid;

class DoctrineUuidType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getBinaryTypeDeclarationSQL(
            [
                'length' => '16',
                'fixed'  => true,
            ],
        );
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Uuid
    {
        if (is_null($value)) {
            return null;
        }

        $uuid = \Ramsey\Uuid\Uuid::fromBytes($value);

        $specificUuidType = $this->specificUuidType();

        return $specificUuidType::fromString($uuid->toString());
    }

    /**
     * @param Uuid|null $value
     * @param AbstractPlatform $platform
     *
     * @return false|string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return !is_null($value) ? self::transform($value->value()): null;
    }

    private static function transform($string)
    {
        return hex2bin(str_replace('-', '', (string) $string));
    }

    public function getName(): string
    {
        return 'uuid_binary';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getBindingType(): int
    {
        return ParameterType::BINARY;
    }

    protected function specificUuidType(): string
    {
        return Uuid::class;
    }
}
