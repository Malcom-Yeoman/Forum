<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class Order04AnswerFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 40; $i++) {
            $answer = new Answer();
            $answer->setContent($faker->paragraph);
            $answer->setDatePosted($faker->dateTimeThisYear);
            $answer->setQuestion($this->getReference('question_' . rand(0, 19)));
            $answer->setUser($this->getReference('user_' . rand(0, 9)));

            $manager->persist($answer);
        }

        $manager->flush();
    }
}