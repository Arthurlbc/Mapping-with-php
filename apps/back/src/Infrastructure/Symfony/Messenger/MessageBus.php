<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Messenger;

use App\Application;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessageBus implements Application\MessageBus
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function transactional(object $message): object
    {
        return $this->messageBus->dispatch($message);
    }
}
