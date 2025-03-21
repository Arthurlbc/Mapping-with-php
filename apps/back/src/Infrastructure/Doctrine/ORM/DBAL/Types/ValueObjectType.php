<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * @template ValueObject of mixed
 * @template Normalization of mixed
 */
abstract class ValueObjectType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    /**
     * @param ?ValueObject $value
     *
     * @return ?Normalization
     */
    public function convertToDatabaseValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        if (null === $value) {
            return null;
        }

        return $this->normalize($value);
    }

    /**
     * @param ?Normalization $value
     *
     * @return ?ValueObject
     */
    public function convertToPHPValue(
        mixed $value,
        AbstractPlatform $platform,
    ): mixed {
        if (null === $value) {
            return null;
        }

        return $this->denormalize($value);
    }

    /**
     * @param ValueObject $valueObject
     *
     * @return Normalization
     */
    abstract protected function normalize(mixed $valueObject): mixed;

    /**
     * @param Normalization $normalized
     *
     * @return ValueObject
     */
    abstract protected function denormalize(mixed $normalized): mixed;
}
