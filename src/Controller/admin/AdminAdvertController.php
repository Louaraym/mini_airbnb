<?php

namespace App\Controller\admin;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminAdvertController
 * @package App\Controller\admin
 */
class AdminAdvertController extends AbstractController
{
    /**
     * @Route("/admin/adverts/{page}", name="admin_adverts_index", requirements={"page"="\d+"})
     * @param Pagination $pagination
     * @param int $page
     * @return Response
     */
    public function index(Pagination $pagination, $page = 1): Response
    {
        $pagination->setClassName(Advert::class)
                    ->setLimit(10)
                    ->setCurrentPage($page);

        return $this->render('admin/advert/index.html.twig', [
            'pagination' => $pagination,
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

    /**
     * @Route("admin/advert/delete/{id}", name="admin_advert_delete", methods={"DELETE"})
     * @param Request $request
     * @param Advert $advert
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if (count($advert->getBookings()) > 0){
            $this->addFlash('warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$advert->getTitle()}</strong> 
                            Car elle possède déjà des réservations !");
        }else if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
            $this->addFlash('success',
                "L'annonce <strong>{$advert->getTitle()}</strong> a été supprimée avec succès !"
            );
        }

        return $this->redirectToRoute('admin_adverts_index');
    }

}
