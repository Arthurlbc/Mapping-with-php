<?php

declare(strict_types=1);

namespace App\Application;

interface MessageBus
{
    public function transactional(object $message): object;
}
