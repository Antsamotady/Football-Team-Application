<?php

namespace App\DataFixtures;

use App\Entity\Player;
use App\Entity\Team;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TeamFistures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i=0; $i<5; $i++) {
            $team = new Team();

            $team
                ->setName(ucfirst($faker->word()))
                ->setCountry(ucfirst($faker->country()))
                ->setMoney($faker->numberBetween(-5000000, 50000000));
            
            $manager->persist($team);

            for($j=0; $j<mt_rand(4, 18); $j++) {
                $player = new Player();

                $player
                    ->setName($faker->firstName())
                    ->setSurname($faker->lastName())
                    ->setTeam($faker->randomElement([$team, null]))
                    ->setPrice($faker->numberBetween(0, 100000))
                    ->setIsAvailableForSale($faker->randomElement([true, false]))
                    ;
                
                $manager->persist($player);
            }
        }

        $manager->flush();
    }
}
