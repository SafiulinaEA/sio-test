<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //cents
        $products = [
                ['Iphone', 10000],
                ['Headphones', 2000],
                ['Case', 1000],
        ];

        foreach ($products as [$name, $price]) {
            $product = new Product();
            $product->setName($name);
            $product->setPrice($price);
            $manager->persist($product);
        }

        $manager->flush();
    }
}