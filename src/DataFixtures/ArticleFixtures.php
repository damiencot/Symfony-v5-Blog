<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = CategoryFixtures::CATEGORIES;
        $faker = Factory::create('fr_FR');
        foreach (range(1,20) as $index) {
            $article = new Article();
            $article->setTitle($faker->words($faker->numberBetween(1, 2), $asText = true));
            $article->setAuthor($faker->name);
            $article->setContent($faker->text(200));
            $article->setPublished($faker->boolean);
            $article->setNbViews($faker->numberBetween(0, 50));
            $category =  $this->getReference('category' . $faker->numberBetween(0, count($categories) - 1));
            $article->addCategory($category);
            $this->addReference('article' . $index, $article);
            if ($faker->boolean)
            {
                $article->setCreatedAt($faker->dateTime());
            }
            $manager->persist($article);
        }
        $manager->flush();
    }
    public function getDependencies()
    {

        return array(
            CategoryFixtures::class,
        );

    }
}
