<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Classe;
use App\Entity\Student;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class StudentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $classes = $manager->getRepository(Classe::class)->findAll();

        for ($i = 0; $i < 120; $i++) {
            $student = new Student();
            $student->setFirstname($faker->firstName);
            $student->setLastname($faker->lastName);
            $student->setGender($faker->randomElement(['Me', 'Mr']));
            $student->setClasse($faker->randomElement($classes));

            $manager->persist($student);
        }

        $manager->flush();
    }
}
