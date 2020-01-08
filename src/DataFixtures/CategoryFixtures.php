<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORIES = ['Fantasy', 'Science Fiction', 'Horreur', 'Drame'];
    public function load(ObjectManager $manager)
    {
        foreach (self::CATEGORIES as $index => $value) {
            $category = new Category();
            $category->setName($value);
            $manager->persist($category);
            $this->addReference('category' . $index, $category);
        }
        $manager->flush();
    }
}
