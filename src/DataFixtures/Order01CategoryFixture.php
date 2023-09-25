<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class Order01CategoryFixture extends Fixture
{
    public const CATEGORIES = ['PHP', 'SYMFONY', 'LARAVEL'];

    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $key => $categoryName) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            $this->addReference('category_' . $key, $category);
        }

        $manager->flush();
    }
}