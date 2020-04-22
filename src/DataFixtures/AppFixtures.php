<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager)
    {
       for ($i=0; $i<40; $i++){
           $ad = new Ad();
           $content = '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>';

           $ad->setTitle($this->faker->sentence)
               ->setCoverImage($this->faker->imageUrl(1000, 350))
               ->setIntroduction($this->faker->paragraph(2))
               ->setContent($content)
               ->setPrice(mt_rand(50,250))
               ->setRooms(mt_rand(1,6));

           for ($j=0, $jMax = mt_rand(3, 5); $j<= $jMax; $j++){
               $image = new Image();
               $image->setUrl($this->faker->imageUrl())
                     ->setCaption($this->faker->sentence)
                     ->setAd($ad);

               $manager->persist($image);
           }

           $manager->persist($ad);
       }

        $manager->flush();
    }
}
