<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Data\ValueObject\Author;
use App\Infrastructure\Doctrine\ORM\Entity\Course;
use App\Infrastructure\Doctrine\ORM\Entity\Member;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $members = [];
        for ($i = 0; $i < 10; ++$i) {
            $member = new Member(
                name: $faker->name(),
                coursesComplete: []
            );

            $members[] = $member;
            $manager->persist($member);
        }

        for ($i = 0; $i < 5; ++$i) {
            $author = new Author(
                firstName: $faker->firstName(),
                lastName: $faker->lastName(),
                organization: $faker->company()
            );

            $selectedMemberIds = array_map(fn ($m) => $m->getId(), array_slice($members, 0, rand(2, 5)));

            $course = new Course(
                name: $faker->sentence(3),
                description: $faker->paragraph(),
                duration: rand(10, 50),
                author: $author,
                memberIds: $selectedMemberIds
            );

            $manager->persist($course);
        }

        $manager->flush();
    }
}
