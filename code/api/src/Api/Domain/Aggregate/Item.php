<?php

declare(strict_types=1);

namespace Api\Domain\Aggregate;

use DateTimeImmutable;
use Shared\Domain\Entity;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

/**
 * @method ItemId id()
 */
final class Item extends Entity
{
    private StringValueObject $name;
    private ItemPrice $price;
    private IntValueObject $stock;
    private DateTimeImmutable $createdAt;

    private function __construct(ItemId $id, StringValueObject $name, ItemPrice $price, IntValueObject $stock)
    {
        parent::__construct($id);

        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(
        ItemId $id,
        StringValueObject $name,
        ItemPrice $price,
        IntValueObject $stock
    ): self {
        return new self($id, $name, $price, $stock);
    }

    public function name(): StringValueObject
    {
        return $this->name;
    }

    public function price(): ItemPrice
    {
        return $this->price;
    }

    public function stock(): IntValueObject
    {
        return $this->stock;
    }

    public function update(ItemPrice $price, IntValueObject $stock): void
    {
        $this->price = $price;
        $this->stock = $stock;
    }

    public function reduceStock(): void
    {
        if ($this->stock->isGreaterThanZero()) {
            $this->stock = $this->stock->decrease();
        }
    }
}