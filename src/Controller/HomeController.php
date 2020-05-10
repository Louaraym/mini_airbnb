<?php

namespace App\Controller;

use App\Repository\AdvertRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param UserRepository $userRepository
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function index(UserRepository $userRepository, AdvertRepository $advertRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'adverts' => $advertRepository->findBestAdverts(6),
            'users' => $userRepository->findBestUsers(3),
        ]);
    }
}
