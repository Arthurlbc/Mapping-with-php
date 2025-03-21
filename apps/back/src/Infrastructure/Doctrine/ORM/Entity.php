<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM;

use Doctrine\ORM\Mapping\ClassMetadata;

interface Entity
{
    /**
     * @param ClassMetadata<self> $metadata
     */
    public static function loadMetaData(ClassMetadata $metadata): void;
}
