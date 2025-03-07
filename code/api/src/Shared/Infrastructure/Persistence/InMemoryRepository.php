<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence;

use Doctrine\ORM\EntityNotFoundException;
use Shared\Common\Collection;
use Shared\Common\TypedCollection;
use Shared\Domain\AggregateRoot;
use Shared\Domain\Uuid;

abstract class InMemoryRepository
{
    protected $elements;

    public function __construct(array $elements = [])
    {
        $aggregateRootClass = static::entityClassName();

        $this->elements = new class([], $aggregateRootClass) extends TypedCollection {
            private $aggregateRootClass;

            public function __construct(array $elements, string $aggregateRootClass)
            {
                $this->aggregateRootClass = $aggregateRootClass;
                parent::__construct($elements);
            }

            protected function type(): string
            {
                return $this->aggregateRootClass;
            }

            /** @return static */
            protected function createFrom(array $elements)
            {
                return new static($elements, $this->aggregateRootClass);
            }
        };

        $this->elements->clear();
        Collection::create($elements)->each(function (AggregateRoot $aggregateRoot): void {
            static::doSave($aggregateRoot);
        });
    }

    protected function doSave(AggregateRoot $aggregateRoot): void
    {
        $this->elements->set($aggregateRoot->id()->value(), $this->deepClone($aggregateRoot));
    }

    protected function doFind(Uuid $aggregateRootId)
    {
        return $this->deepClone($this->elements->get($aggregateRootId->value()) ?? $this->nullResult());
    }

    protected function doFindOrFail(Uuid $aggregateRootId)
    {
        $aggregateRoot = $this->doFind($aggregateRootId);

        if (is_null($aggregateRoot)) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(
                static::entityClassName(),
                [$aggregateRootId->value()]
            );
        }

        return $aggregateRoot;
    }

    protected function deepClone($object)
    {
        return unserialize(serialize($object));
    }

    public function doRemove(AggregateRoot $aggregateRoot): void
    {
        $this->elements->remove($aggregateRoot->id()->value());
    }

    protected function nullResult()
    {
        return null;
    }

    public function all(): Collection
    {
        return $this->elements;
    }

    abstract protected static function entityClassName(): string;
}
