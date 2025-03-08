<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Shared\Common\TypedCollection;
use Shared\Domain\StringValueObject;
use Throwable;

/**
 * @method Item firstOrFail(Throwable $customException = null)
 */
final class ItemCollection extends TypedCollection
{
    protected function type(): string
    {
        return Item::class;
    }

    public function filterByName(StringValueObject $name): self
    {
        return $this->filter(static fn (Item $item) => $item->name()->equalsTo($name));
    }

    public function filterVended(): self
    {
        return $this->filter(static fn (Item $item) => $item->isVended()->isTrue());
    }
}
