<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Entity;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

class Member extends Domain\Data\Model\Member implements Entity
{
    public static function loadMetaData(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('members');

        $builder->createField('id', Types::STRING)
            ->nullable(false)
            ->makePrimaryKey()
            ->build();

        $builder->createField('name', Types::STRING)
            ->nullable(false)
            ->build();

        $builder->createField('coursesComplete', Types::SIMPLE_ARRAY)
            ->nullable(true)
            ->build();
    }
}
