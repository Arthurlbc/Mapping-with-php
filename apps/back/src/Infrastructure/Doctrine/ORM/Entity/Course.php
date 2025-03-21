<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Entity;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\DBAL\Types\ValueObjectType\AuthorType;
use App\Infrastructure\Doctrine\ORM\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;

class Course extends Domain\Data\Model\Course implements Entity
{
    public static function loadMetaData(ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('courses');

        $builder->createField('id', Types::STRING)
            ->nullable(false)
            ->makePrimaryKey()
            ->build();

        $builder->createField('name', Types::STRING)
            ->nullable(false)
            ->build();

        $builder->createField('description', Types::STRING)
            ->nullable(false)
            ->build();

        $builder->createField('duration', Types::INTEGER)
            ->nullable(false)
            ->build();

        $builder->createField('author', AuthorType::NAME)
            ->nullable(true)
            ->build();

        $builder->createField('memberIds', Types::SIMPLE_ARRAY)
            ->nullable(true)
            ->build();
    }
}
