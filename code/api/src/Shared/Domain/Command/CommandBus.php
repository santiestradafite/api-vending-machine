<?php

declare(strict_types=1);

namespace Shared\Domain\Command;

interface CommandBus
{
    public function dispatch(Command $command): void;
}
