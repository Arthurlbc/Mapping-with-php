<?php

declare(strict_types=1);

namespace App\Domain\Data\Model;

use App\Domain\Data\ValueObject\Author;

class Course
{
    protected string $id;

    /**
     * @param array<string> $memberIds
     */
    public function __construct(
        protected string $name,
        protected string $description,
        protected int $duration,
        protected Author $author,
        protected array $memberIds,
    ) {
        $id = uuid_create();
        if (is_string($id)) {
            $this->id = uniqid($id);
        } else {
            throw new \RuntimeException('Failed to generate UUID');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function addMember(string $memberId): void
    {
        $this->memberIds[] = $memberId;
    }

    public function hasMember(string $memberId): bool
    {
        return in_array($memberId, $this->memberIds);
    }

    public function removeMember(string $memberIdToRemove): void
    {
        if ($this->hasMember($memberIdToRemove)) {
            $this->memberIds = array_filter($this->memberIds, fn ($memberId) => $memberId !== $memberIdToRemove);
        }
    }

    /**
     * @return array<string>
     */
    public function getMemberIds(): array
    {
        return $this->memberIds;
    }
}
