<?php

declare(strict_types=1);

namespace Shared\Domain\Event;

use DateTimeImmutable;
use Shared\Domain\Uuid;

abstract class DomainEvent
{
    private string $aggregateId;
    private string $eventId;
    private ?DateTimeImmutable $occurredOn;

    public function __construct(string $aggregateId, string $eventId = null, DateTimeImmutable $occurredOn = null)
    {
        $this->aggregateId = $aggregateId;
        $this->eventId     = $eventId ?: Uuid::generate()->value();
        $this->occurredOn  = $occurredOn ?? new DateTimeImmutable();
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): ?DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
