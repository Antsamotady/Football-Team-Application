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
            $gender = $faker->randomElement(['Me', 'Mr']);

            if (!is_string($gender)) {
                $gender = 'Mr'; // default fallback
            }
            $student->setGender($gender);

            /** @var Classe|null $classe */
            $classe = $faker->randomElement($classes);
            $student->setClasse($classe);

            $manager->persist($student);
        }

        $manager->flush();
    }
}
