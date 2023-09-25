<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class Order03QuestionFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $question = new Question();
            $question->setTitle($faker->sentence);
            $question->setContent($faker->paragraph);
            $question->setDatePosted($faker->dateTimeThisYear);
            $question->setCategory($this->getReference('category_' . rand(0, 2)));
            $question->setUser($this->getReference('user_' . rand(0, 9)));

            $this->addReference('question_' . $i, $question);
            $manager->persist($question);
        }

        $manager->flush();
    }
}