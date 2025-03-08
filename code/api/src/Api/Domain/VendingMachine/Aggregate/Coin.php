<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Shared\Domain\BoolValueObject;
use Shared\Domain\Entity;

/**
 * @method CoinId id()
 */
final class Coin extends Entity
{
    private CoinValue $value;
    private BoolValueObject $isInserted;
    private BoolValueObject $isReturned;

    private function __construct(CoinId $id, CoinValue $value)
    {
        parent::__construct($id);

        $this->value = $value;
        $this->isInserted = BoolValueObject::false();
        $this->isReturned = BoolValueObject::false();
    }

    public static function create(
        CoinId $id,
        CoinValue $value
    ): self {
        return new self($id, $value);
    }

    public function value(): CoinValue
    {
        return $this->value;
    }

    public function isInserted(): BoolValueObject
    {
        return $this->isInserted;
    }

    public function isReturned(): BoolValueObject
    {
        return $this->isReturned;
    }

    public function insert(): void
    {
        $this->isInserted = BoolValueObject::true();
        $this->isReturned = BoolValueObject::false();
    }

    public function return(): void
    {
        $this->isInserted = BoolValueObject::false();
        $this->isReturned = BoolValueObject::true();
    }

    public function collect(): void
    {
        $this->isInserted = BoolValueObject::false();
        $this->isReturned = BoolValueObject::false();
    }
}