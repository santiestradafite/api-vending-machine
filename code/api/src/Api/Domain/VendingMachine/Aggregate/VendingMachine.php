<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Api\Domain\VendingMachine\Event\CoinInsertedEvent;
use Api\Domain\VendingMachine\Event\ItemVendedEvent;
use Api\Domain\VendingMachine\Event\VendingMachineCreatedEvent;
use Api\Domain\VendingMachine\Exception\ItemNotVendedException;
use Closure;
use Shared\Domain\AggregateRoot;
use Shared\Domain\FloatValueObject;
use Shared\Domain\StringValueObject;

/**
 * @method VendingMachineId id()
 */
class VendingMachine extends AggregateRoot
{
    private StringValueObject $name;
    /** @var ItemCollection $items */
    private $items;
    /** @var CoinCollection $coins */
    private $coins;

    private function __construct(
        VendingMachineId $id,
        StringValueObject $name,
        ItemCollection $items,
        CoinCollection $coins
    ) {
        parent::__construct($id);

        $this->name = $name;
        $this->setItems($items);
        $this->setCoins($coins);

        $this->record(VendingMachineCreatedEvent::create($id, $name, $items, $coins));
    }

    public static function create(
        VendingMachineId $id,
        StringValueObject $name,
        ItemCollection $items,
        CoinCollection $coins
    ): self {
        return new self($id, $name, $items, $coins);
    }

    private function setItems(ItemCollection $items): void
    {
        $items->each(fn (Item $item) => $item->setVendingMachine($this));

        $this->items = $items;
    }

    private function setCoins(CoinCollection $coins): void
    {
        $coins->each(fn (Coin $coin) => $coin->setVendingMachine($this));

        $this->coins = $coins;
    }

    public function name(): StringValueObject
    {
        return $this->name;
    }

    public function items(): ItemCollection
    {
        return ItemCollection::cloneIndexed($this->items);
    }

    public function coins(): CoinCollection
    {
        return CoinCollection::cloneIndexed($this->coins);
    }

    public function insertCoin(Coin $coin): void
    {
        $coin->insert();
        $coin->setVendingMachine($this);
        $this->coins->add($coin);

        $this->record(CoinInsertedEvent::create($coin->id(), $this->id(), $coin->value()));
    }

    public function vendItem(ItemId $itemId): void
    {
        $item = $this->items()->get($itemId->value());

        if (null === $item) {
            throw ItemNotVendedException::becauseItemIsNotFound($itemId);
        }

        $this->assertItemCanBeVended($item);
        $item->vend();
        $this->returnChange($item);
        $this->collectInsertedCoins();

        $this->record(ItemVendedEvent::create($item->id(), $this->id(), $item->name(), $item->price(), $item->stock()));
    }

    private function assertItemCanBeVended(Item $item): void
    {
        if (null !== $this->vendedItem()) {
            throw ItemNotVendedException::becauseVendedItemIsNotCollected();
        }

        if ($item->price()->isGreaterThan($this->insertedMoney())) {
            throw ItemNotVendedException::becauseInsertedMoneyIsNotEnough($item, $this->insertedMoney());
        }
    }

    private function returnChange(Item $item): void
    {
        $neededChange = $this->insertedMoney()->subtract($item->price());

        if (!$neededChange->isGreaterThanZero()) {
            return;
        }

        $this->coins()->sortByValue()->each($this->calculateChangeFn($neededChange));

        if ($neededChange->isGreaterThanZero()) {
            throw ItemNotVendedException::becauseTheMachineHasNoChange($item->id());
        }
    }

    private function insertedMoney(): FloatValueObject
    {
        return $this->insertedCoins()->reduce(
            static fn (FloatValueObject $carry, Coin $coin) => $carry->add($coin->value()),
            FloatValueObject::zero()
        );
    }

    public function insertedCoins(): CoinCollection
    {
        return $this->coins()->filterInserted();
    }

    private function calculateChangeFn(FloatValueObject &$neededChange): Closure
    {
        return static function (Coin $coin) use (&$neededChange) {
            while ($neededChange->isGreaterThanOrEqualsTo($coin->value()) && $coin->isReturned()->isFalse()) {
                $neededChange = $neededChange->subtract($coin->value());
                $coin->return();
            }
        };
    }

    private function collectInsertedCoins(): void
    {
        $this->insertedCoins()->each(fn (Coin $coin) => $coin->collect());
    }

    public function returnInsertedCoins(): void
    {
        $this->insertedCoins()->each(fn (Coin $coin) => $coin->return());
    }

    public function collectVendedItemAndReturnedCoins(): void
    {
        $this->vendedItem()?->collect();
        $this->returnedCoins()->each(fn (Coin $coin) => $this->coins->removeElement($coin));
    }

    public function vendedItem(): ?Item
    {
        return $this->items()->filterVended()->first() ?: null;
    }

    public function returnedCoins(): CoinCollection
    {
        return $this->coins()->filterReturned();
    }
}