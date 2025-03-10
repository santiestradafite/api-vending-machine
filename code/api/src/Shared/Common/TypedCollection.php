<?php

declare(strict_types=1);

namespace Shared\Common;

use Assert\Assertion;
use Assert\InvalidArgumentException;
use BadMethodCallException;
use Closure;
use Shared\Common\Exception\TypedCollectionException;
use Throwable;

abstract class TypedCollection extends Collection
{
    public function __construct(array $elements)
    {
        $this->assertAllIsInstanceOf($elements);

        parent::__construct($elements);
    }

    private function assertAllIsInstanceOf(array $elements): void
    {
        try {
            Assertion::allIsInstanceOf($elements, $this->type());
        } catch (InvalidArgumentException $exception) {
            throw TypedCollectionException::create($exception->getMessage());
        }
    }

    abstract protected function type(): string;

    public function add($element): void
    {
        $this->assertIsInstanceOf($element);

        parent::add($element);
    }

    private function assertIsInstanceOf($element): void
    {
        try {
            Assertion::isInstanceOf($element, $this->type());
        } catch (InvalidArgumentException $exception) {
            throw TypedCollectionException::create($exception->getMessage());
        }
    }

    public function set($key, $value): void
    {
        $this->assertIsInstanceOf($value);

        parent::set($key, $value);
    }

    protected static function indexBy(): callable
    {
        throw new BadMethodCallException("method indexBy not implemented yet");
    }

    /**
     * @throws TypedCollectionException
     * @throws Throwable
     */
    public function firstOrFail(Throwable $customException = null)
    {
        $first = parent::first();

        $throwExceptionFn = $this->selectExceptionClosureToThrowFrom($customException, 'first');

        if (!$first) {
            $throwExceptionFn();
        }

        return $first;
    }

    private function customExceptionFn(Throwable $customException): Closure
    {
        return static function () use ($customException) {
            throw $customException;
        };
    }

    private function defaultGetElementExceptionFn(string $action): Closure
    {
        $messageError = sprintf('Cannot return %s element because not exist in typed collection.', $action);

        return static function () use ($messageError) {
            throw TypedCollectionException::create($messageError);
        };
    }

    private function selectExceptionClosureToThrowFrom(?Throwable $customException, string $action): Closure
    {
        return $customException
            ? $this->customExceptionFn($customException)
            : $this->defaultGetElementExceptionFn($action);
    }

    public static function cloneIndexed(iterable $collection, callable $indexBy = null): static
    {
        $clonedCollection = static::createEmpty();

        foreach ($collection as $item) {
            $clonedCollection->set($indexBy ? $indexBy($item) : static::indexBy()($item), $item);
        }

        return $clonedCollection;
    }
}
