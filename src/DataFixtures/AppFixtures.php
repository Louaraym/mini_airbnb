<?php

namespace App\DataFixtures;

use App\Entity\Advert;
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
       for ($i=0; $i<42; $i++){
           $advert = new Advert();
           $content = '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>';
           $imageUrl = 'https://picsum.photos/200?random=' . mt_rand(1, 999);

           $advert->setTitle($this->faker->sentence)
               ->setCoverImage($imageUrl)
               ->setIntroduction($this->faker->paragraph(2))
               ->setContent($content)
               ->setPrice(mt_rand(50,250))
               ->setRooms(mt_rand(1,6));

           for ($j=0, $jMax = mt_rand(3, 5); $j<= $jMax; $j++){
               $image = new Image();
               $imageUrl = 'https://picsum.photos/200?random=' . mt_rand(1, 999);
               $image->setUrl($imageUrl)
                     ->setCaption($this->faker->sentence)
                     ->setAdvert($advert);

               $manager->persist($image);
           }

           $manager->persist($advert);
       }

        $manager->flush();
    }
}
