<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $encoder;
    private $entityManager;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager)
    {
        $this->faker = Factory::create('fr_FR');
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadData();
        $manager->flush();
    }

    public function loadData(): void
    {
        $users = [];
        $genres = ['male', 'female'];

        for ($i=0; $i<20; $i++){

            $user = new User();
            $genre = $this->faker->randomElement($genres);
            $description = '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>';
            $avatarUrl = 'https://randomuser.me/api/portraits/';
            $avatarId = $this->faker->numberBetween(1,99).'.jpg';
            $avatarUrl .= ($genre === 'male' ? 'men/' : 'women/') . $avatarId;

            $user->setFirstName($this->faker->firstName($genre))
                ->setLastName($this->faker->lastName)
                ->setEmail(strtolower($user->getLastName().'@gmail.com'))
                ->setIntroduction($this->faker->paragraph(2))
                ->setDescription($description)
                ->setPassword($this->encoder->encodePassword($user, $user->getLastName()))
                ->setAvatarUrl($avatarUrl)
            ;
            $this->entityManager->persist($user);
            $users[] = $user;
        }

        for ($i=0; $i<42; $i++){
            $advert = new Advert();
            $content = '<p>' . implode('</p><p>', $this->faker->paragraphs(5)) . '</p>';
            $imageUrl = 'https://picsum.photos/200?random=' . mt_rand(1, 999);

            $user = $users[mt_rand(0, count($users)-1)];

            $advert->setTitle($this->faker->sentence)
                ->setCoverImage($imageUrl)
                ->setIntroduction($this->faker->paragraph(2))
                ->setContent($content)
                ->setPrice(mt_rand(50,250))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user)
            ;

            for ($j=0, $jMax = mt_rand(3, 5); $j<= $jMax; $j++){
                $image = new Image();
                $imageUrl = 'https://picsum.photos/200?random=' . mt_rand(1, 999);
                $image->setUrl($imageUrl)
                    ->setCaption($this->faker->sentence)
                    ->setAdvert($advert);

                $this->entityManager->persist($image);
            }

            $this->entityManager->persist($advert);
        }
    }

}
