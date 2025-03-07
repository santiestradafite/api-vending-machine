<?php

declare(strict_types=1);

namespace Shared\Common;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionFunction;
use function Lambdish\Phunctional\all;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\sort;

class Collection extends ArrayCollection
{
    public static function create(array $elements)
    {
        return new static($elements);
    }

    public static function createEmpty()
    {
        return new static([]);
    }

    public function extract()
    {
        $elements = $this->toArray();
        $this->clear();

        return $this->createFrom($elements);
    }

    public function each(callable $fn): void
    {
        foreach ($this->getIterator() as $key => $element) {
            $fn($element, $key);
        }
    }

    public function all(callable $predicate): bool
    {
        return all($predicate, $this->toArray());
    }

    public function sort(callable $criteria)
    {
        return $this->createFrom(
            sort($criteria, $this->toArray())
        );
    }

    public function reduce(Closure $func, $initial = null): mixed
    {
        return array_reduce($this->toArray(), $func, $initial);
    }

    public function untypedMap(callable $fn): Collection
    {
        return new self(map($fn, $this->toArray()));
    }

    public function unique(): Collection
    {
        return self::create(array_unique($this->getValues()));
    }

    public function exists(Closure $p): bool
    {
        $closure = new ReflectionFunction($p);

        if ($closure->getNumberOfParameters() === 1) {
            return $this->existsElement($p);
        }

        return parent::exists($p);
    }

    private function existsElement(Closure $fn): bool
    {
        foreach ($this->toArray() as $element) {
            if ($fn($element)) {
                return true;
            }
        }

        return false;
    }
}
