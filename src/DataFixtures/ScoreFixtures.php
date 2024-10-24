<?php

namespace App\DataFixtures;

use App\Entity\Student;
use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ScoreFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $students = $manager->getRepository(Student::class)->findOneBy(['firstname' => 'Marisa']);
        dd($students);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
