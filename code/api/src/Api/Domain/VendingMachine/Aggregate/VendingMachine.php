<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Api\Domain\VendingMachine\Event\VendingMachineCreatedEvent;
use Api\Domain\VendingMachine\Exception\ItemNotVendedException;
use Closure;
use Shared\Domain\AggregateRoot;
use Shared\Domain\FloatValueObject;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

/**
 * @method VendingMachineId id()
 */
class VendingMachine extends AggregateRoot
{
    private ItemCollection $items;
    private CoinCollection $coins;

    private function __construct(VendingMachineId $id, ItemCollection $items, CoinCollection $coins)
    {
        parent::__construct($id);

        $this->setItems($items);
        $this->setCoins($coins);

        $this->record(VendingMachineCreatedEvent::create($id, $items, $coins));
    }

    public static function create(VendingMachineId $id, ItemCollection $items, CoinCollection $coins): self
    {
        return new self($id, $items, $coins);
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

    public function items(): ItemCollection
    {
        return $this->items;
    }

    public function coins(): CoinCollection
    {
        return $this->coins;
    }

    public function insertItem(StringValueObject $name, ItemPrice $price, IntValueObject $stock): void
    {
        $itemsByName = $this->items->filterByName($name);

        if ($itemsByName->isEmpty()) {
            $this->addItem(Item::create(ItemId::generate(), $name, $price, $stock));
        } else {
            $itemsByName->firstOrFail()->update($price, $stock);
        }
    }

    private function addItem(Item $item): void
    {
        $item->setVendingMachine($this);

        $this->items->add($item);
    }

    public function insertCoin(Coin $coin): void
    {
        $coin->insert();
        $this->addCoin($coin);
    }

    private function addCoin(Coin $coin): void
    {
        $coin->setVendingMachine($this);

        $this->coins->add($coin);
    }

    public function vendItem(StringValueObject $itemName): void
    {
        $itemsByName = $this->items->filterByName($itemName);
        if ($itemsByName->isEmpty()) {
            throw ItemNotVendedException::becauseItemIsNotFound($itemName);
        }

        $item = $itemsByName->firstOrFail();
        if ($item->price()->isGreaterThan($this->insertedMoney())) {
            throw ItemNotVendedException::becauseInsertedMoneyIsNotEnough($itemName);
        }

        $item->vend();
        $this->returnChange($item);
        $this->collectInsertedCoins();
    }

    private function collectInsertedCoins(): void
    {
        $this->insertedCoins()->each(fn (Coin $coin) => $coin->collect());
    }

    public function insertedCoins(): CoinCollection
    {
        return $this->coins->filterInserted();
    }

    private function returnChange(Item $item): void
    {
        $neededChange = $this->insertedMoney()->subtract($item->price());

        if (!$neededChange->isGreaterThanZero()) {
            return;
        }

        $this->coins->sortByValue()->each($this->calculateChangeFn($neededChange));

        if ($neededChange->isGreaterThanZero()) {
            throw ItemNotVendedException::becauseTheMachineHasNoChange($item->name());
        }
    }

    private function insertedMoney(): FloatValueObject
    {
        return $this->insertedCoins()->reduce(
            static fn (FloatValueObject $carry, Coin $coin) => $carry->add($coin->value()),
            FloatValueObject::zero()
        );
    }

    private function calculateChangeFn(FloatValueObject &$neededChange): Closure
    {
        return static function (Coin $coin) use (&$neededChange) {
            while ($neededChange->isGreaterThanOrEqualsTo($coin->value())) {
                $neededChange = $neededChange->subtract($coin->value());
                $coin->return();
            }
        };
    }

    public function returnInsertedCoins(): void
    {
        $this->insertedCoins()->each(fn (Coin $coin) => $coin->return());
    }

    public function collectVendedItemAndChange(): void
    {
        $this->vendedItem()?->collect();
        $this->returnedCoins()->each(fn (Coin $coin) => $this->coins->removeElement($coin));
    }

    public function vendedItem(): ?Item
    {
        return $this->items->filterVended()->first() ?: null;
    }

    public function returnedCoins(): CoinCollection
    {
        return $this->coins->filterReturned();
    }
}