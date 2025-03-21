<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\DBAL\Types\ValueObjectType;

use App\Domain;
use App\Domain\Data\ValueObject\Author;
use App\Infrastructure\Doctrine\ORM\DBAL\Types\ValueObjectType;

/**
 * @template ValueObject of object
 *
 * @extends ValueObjectType<Domain\Data\ValueObject\Money, string>
 */
final class AuthorType extends ValueObjectType
{
    public const NAME = 'author';

    public function getName(): string
    {
        return self::NAME;
    }

    public function normalize(mixed $author): string
    {
        if (!$author instanceof Author) {
            throw new \InvalidArgumentException('It should be an instance of Author.');
        }

        $data = json_encode([
            'firstName' => $author->firstName,
            'lastName' => $author->lastName,
            'organization' => $author->organization,
        ]);

        if (false === $data) {
            throw new \InvalidArgumentException('Failed to encode Author object to JSON.');
        }

        return $data;
    }

    public function denormalize(mixed $author): Author
    {
        $data = json_decode($author, true);

        if (!is_array($data)) {
            throw new \InvalidArgumentException('Invalid JSON format for Author.');
        }

        if (
            !is_string($data['firstName'])
            || !is_string($data['lastName'])
            || !is_string($data['organization'])
        ) {
            throw new \InvalidArgumentException('Invalid data types for Author properties.');
        }

        return new Author($data['firstName'], $data['lastName'], $data['organization']);
    }
}
