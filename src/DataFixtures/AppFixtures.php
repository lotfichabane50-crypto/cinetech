<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 1. Création des 5 catégories
        $categoryNames = ['Science-Fiction', 'Action', 'Thriller', 'Comédie', 'Drame'];
        $categories = [];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        // 2. Création de 30 films reliés aléatoirement à une catégorie
        for ($i = 0; $i < 30; $i++) {
            $movie = new Movie();
            $movie->setTitle(ucfirst($faker->words(3, true)))
                ->setSynopsis($faker->paragraphs(2, true))
                ->setDuration($faker->numberBetween(80, 180))
                ->setReleaseDate($faker->dateTimeBetween('-20 years', 'now'))
                ->setRating($faker->randomFloat(1, 1, 5))
                ->setPoster('https://picsum.photos/seed/' . ($i + 1) . '/400/600')
                ->setCategory($faker->randomElement($categories));

            $manager->persist($movie);
        }

        // Un seul flush() en sortie de boucle
        $manager->flush();
    }
}