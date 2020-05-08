<?php

namespace App\Controller\admin;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdvertAdminController
 * @package App\Controller\admin
 */
class AdvertAdminController extends AbstractController
{
    /**
     * @Route("/admin/adverts", name="admin_adverts_index")
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function index(AdvertRepository $advertRepository): Response
    {
        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $advertRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/adverts/edit/{slug}-{id}", name="admin_advert_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param Advert $advert
     * @param String $slug
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Advert $advert, String $slug, EntityManagerInterface $entityManager): Response
    {
        $newSlug = $advert->getSlug();
        if ($newSlug !== $slug){
            return $this->redirectToRoute('advert_show', [
                'id' => $advert->getId(),
                'slug' => $newSlug,
            ], 301);
        }

        $form = $this->createForm(AdvertType::class, $advert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $advert->setUpdatedAt(new \DateTime());
            $images = $advert->getImages();

            if ($images){
                foreach ($images as $image){
                    $image->setAdvert($advert);
                }
            }

            $entityManager->flush();

            $this->addFlash('success',
                "L'annonce <strong>{$advert->getTitle()}</strong> a été modifiée avec succès !"
            );
            return $this->redirectToRoute('advert_show', [
                'slug' => $advert->getSlug(),
                'id' => $advert->getId()
            ]);
        }

        return $this->render('admin/advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

}
