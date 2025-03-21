<?php

declare(strict_types=1);

namespace spec\App\Infrastructure\Doctrine\ORM\DBAL\Types\ValueObjectType;

use App\Domain\Data\ValueObject\Author;
use App\Infrastructure\Doctrine\ORM\DBAL\Types\ValueObjectType\AuthorType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class AuthorTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AuthorType::class);
    }

    function it_should_have_name()
    {
        $this->getName()->shouldReturn('author');
    }

    function it_should_normalize_author_object()
    {
        $author = new Author('John', 'Doe', 'Company');

        $this->normalize($author)->shouldReturn(json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'organization' => 'Company',
        ]));
    }

    function it_should_denormalize_valid_author_json()
    {
        $authorJson = json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'organization' => 'Company',
        ]);

        $this->denormalize($authorJson)->shouldReturnAnInstanceOf(Author::class);
    }

    function it_should_throw_exception_when_denormalizing_invalid_json()
    {
        $invalidJson = 'invalid json string';

        $this->shouldThrow(new \InvalidArgumentException('Invalid JSON format for Author.'))
            ->during('denormalize', [$invalidJson]);
    }

    function it_should_throw_exception_when_denormalizing_invalid_property_types()
    {
        $invalidJson = json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'organization' => 12345,
        ]);

        $this->shouldThrow(new \InvalidArgumentException('Invalid data types for Author properties.'))
            ->during('denormalize', [$invalidJson]);
    }
}
