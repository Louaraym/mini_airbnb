<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}-{id}", name="user_show", requirements={"slug": "[a-z0-9\-]*"})
     * @param User $user
     * @param String $slug
     * @return Response
     */
    public function index(User $user, String $slug): Response
    {
        $newSlug = $user->getSlug();
        if ($newSlug !== $slug){
            return $this->redirectToRoute('user_show', [
                'id' => $user->getId(),
                'slug' => $newSlug,
            ], 301);
        }
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }
}
