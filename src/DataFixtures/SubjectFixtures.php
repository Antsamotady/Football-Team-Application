<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Student;
use App\Entity\StudentSubject;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SubjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $students = $manager->getRepository(Student::class)->findAll();

        foreach ($students as $student) {
            for ($i = 1; $i <= 6; $i++) {
                $subject = new StudentSubject();
                $subject->setName("Topic $i");
                $subject->setScore($faker->numberBetween(0, 20));
                $subject->setStudent($student);

                $manager->persist($subject);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            StudentFixtures::class,
        ];
    }
}
