<?php

declare(strict_types=1);

namespace App\Domain\Data\Model;

class Member
{
    protected string $id;

    /**
     * @param array<string> $coursesComplete
     */
    public function __construct(
        protected string $name,
        protected array $coursesComplete,
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
}
