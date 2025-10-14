<?php

namespace App\DataFixtures;

use App\Entity\Projet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProjetFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR'); // Génère des données françaises

        for ($i = 0; $i < 5; $i++) {
            $projet = new Projet();
            $projet->setNom($faker->sentence(2));

            // Générer createdAt aléatoire
            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year', 'now')
            );

            // Générer updatedAt > createdAt
            $updatedAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween($createdAt->format('Y-m-d H:i:s'), 'now')
            );

            $projet->setCreatedAt($createdAt);
            $projet->setUpdatedAt($updatedAt);


            $manager->persist($projet);
        }

        $manager->flush();
    }
}
