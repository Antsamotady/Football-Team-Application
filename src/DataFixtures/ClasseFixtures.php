<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Classe;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ClasseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $classNames = ['Class A', 'Class B', 'Class C', 'Class D'];

        foreach ($classNames as $name) {
            $classe = new Classe();
            $classe->setName($name);

            $manager->persist($classe);
        }

        //$manager->flush();
    }
}
