<?php

namespace App\DataFixtures;

use App\Enum\typeContrat;
use App\Entity\Employe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EmployeFixtures extends Fixture
{

    public static function getGroups(): array
    {
        return ['employe'];
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        // Quelques statuts possibles
        $statutValues = array_map(fn($c) => $c->value, typeContrat::cases());

        for ($i = 0; $i < 5; $i++) {
            $employe = new Employe();

            $employe->setNom($faker->lastName);
            $employe->setPrenom($faker->firstName);
            $employe->setEmail($faker->unique()->safeEmail);

            // Date d'entrée aléatoire entre -3 ans et aujourd’hui
            $employe->setDateEntree($faker->dateTimeBetween('-3 years', 'now'));
            // Statut aléatoire parmi la liste
            // statut : convertir la string choisie en enum
            $statutString = $faker->randomElement($statutValues);
            $statutEnum   = typeContrat::tryFrom($statutString) ?? typeContrat::CDI;
            $employe->setStatut($statutEnum);

            // Dates de création et de mise à jour
            $createdAt = \DateTimeImmutable::createFromMutable(
                $faker->dateTimeBetween('-1 year', 'now')
            );

            // updatedAt >= createdAt ou null (1 chance sur 3)
            $updatedAt = $faker->boolean(70)
                ? \DateTimeImmutable::createFromMutable(
                    $faker->dateTimeBetween($createdAt->format('Y-m-d H:i:s'), 'now')
                )
                : null;

            $employe->setCreatedAt($createdAt);
            $employe->setUpdatedAt($updatedAt);

            $manager->persist($employe);
        }
        $manager->flush();
    }
}
