<?php

declare(strict_types=1);

namespace Api\Domain\Aggregate;

use Api\Domain\Event\VendingMachineCreatedEvent;
use Api\Domain\Exception\ItemNotVendedException;
use Shared\Domain\FloatValueObject;
use Closure;
use Shared\Domain\AggregateRoot;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

/**
 * @method VendingMachineId id()
 */
class VendingMachine extends AggregateRoot
{
    private ItemCollection $items;
    private CoinCollection $availableCoins;
    private CoinCollection $insertedCoins;
    private CoinCollection $returnedCoins;
    private ?Item $vendedItem;

    private function __construct(VendingMachineId $id, ItemCollection $items, CoinCollection $availableCoins)
    {
        parent::__construct($id);

        $this->items = $items;
        $this->availableCoins = $availableCoins;
        $this->insertedCoins = CoinCollection::createEmpty();
        $this->returnedCoins = CoinCollection::createEmpty();
        $this->vendedItem = null;

        $this->record(VendingMachineCreatedEvent::create($id, $items, $availableCoins));
    }

    public static function create(VendingMachineId $id, ItemCollection $items, CoinCollection $availableCoins): self
    {
        return new self($id, $items, $availableCoins);
    }

    public function items(): ItemCollection
    {
        return $this->items;
    }

    public function availableCoins(): CoinCollection
    {
        return $this->availableCoins;
    }

    public function insertedCoins(): CoinCollection
    {
        return $this->insertedCoins;
    }

    public function returnedCoins(): CoinCollection
    {
        return $this->returnedCoins;
    }

    public function vendedItem(): ?Item
    {
        return $this->vendedItem;
    }

    public function addItem(StringValueObject $name, ItemPrice $price, IntValueObject $stock): void
    {
        $itemsByName = $this->items->filterByName($name);

        if ($itemsByName->isEmpty()) {
            $this->items->add(Item::create(ItemId::generate(), $name, $price, $stock));
        } else {
            $itemsByName->firstOrFail()->update($price, $stock);
        }
    }

    public function insertCoin(Coin $coin): void
    {
        $this->insertedCoins->add($coin);
        $this->availableCoins->add($coin);
    }

    public function insertedMoney(): FloatValueObject
    {
        return $this->insertedCoins->reduce(
            static fn (FloatValueObject $carry, Coin $coin) => $carry->add($coin->value()),
            FloatValueObject::zero()
        );
    }

    public function returnInsertedCoins(): void
    {
        $this->returnedCoins = $this->insertedCoins;
        $this->clearInsertedCoins();
        $this->recalculateAvailableCoins();
    }

    private function clearInsertedCoins(): void
    {
        $this->insertedCoins = CoinCollection::createEmpty();
    }

    private function recalculateAvailableCoins(): void
    {
        $this->returnedCoins->each(fn (Coin $returnedCoin) => $this->availableCoins->removeElement($returnedCoin));
    }

    public function vendItem(StringValueObject $itemName): void
    {
        $itemsByName = $this->items->filterByName($itemName);
        if ($itemsByName->isEmpty()) {
            throw ItemNotVendedException::becauseItemIsNotFound($itemName);
        }

        $item = $itemsByName->firstOrFail();
        if (!$item->stock()->isGreaterThanZero()) {
            throw ItemNotVendedException::becauseItemHasNoStock($itemName);
        }

        if ($item->price()->isGreaterThan($this->insertedMoney())) {
            throw ItemNotVendedException::becauseInsertedMoneyIsNotEnough($itemName);
        }

        $this->calculateChange($item);
        $item->reduceStock();
        $this->clearInsertedCoins();
        $this->recalculateAvailableCoins();
        $this->vendedItem = $item;
    }

    private function calculateChange(Item $item): void
    {
        $neededChange = $this->insertedMoney()->subtract($item->price());

        if (!$neededChange->isGreaterThanZero()) {
            return;
        }

        $this->availableCoins->sortByValue()->each($this->calculateChangeFn($neededChange));

        if ($neededChange->isGreaterThanZero()) {
            throw ItemNotVendedException::becauseTheMachineHasNoChange($item->name());
        }
    }

    private function calculateChangeFn(FloatValueObject &$neededChange): Closure
    {
        return function (Coin $availableCoin) use (&$neededChange) {
            while ($neededChange->isGreaterThanOrEqualsTo($availableCoin->value())) {
                $neededChange = $neededChange->subtract($availableCoin->value());
                $this->returnedCoins->add($availableCoin);
            }
        };
    }

    public function clearVendedItemAndChange(): void
    {
        $this->vendedItem = null;
        $this->returnedCoins = CoinCollection::createEmpty();
    }
}