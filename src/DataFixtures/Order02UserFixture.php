<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class Order02UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail);
            $user->setRoles([]);  // Vous pouvez ajouter des rôles ici si nécessaire
            $user->setPassword($faker->password);
            $user->setPseudo($faker->userName);

            $manager->persist($user);

            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}