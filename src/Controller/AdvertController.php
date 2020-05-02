<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/adverts")
 */
class AdvertController extends AbstractController
{
    /**
     * @Route("/", name="advert_index", methods={"GET"})
     * @param AdvertRepository $advertRepository
     * @return Response
     */
    public function index(AdvertRepository $advertRepository): Response
    {
        return $this->render('advert/index.html.twig', [
            'adverts' => $advertRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add/new", name="advert_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdvertType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Advert $advert */
            $advert = $form->getData();

            $images = $advert->getImages();

            if ($images){
                foreach ($images as $image){
                    $image->setAdvert($advert);
                }
            }

            $advert->setAuthor($this->getUser());

            $entityManager->persist($advert);
            $entityManager->flush();

            $this->addFlash('success', 'Votre annonce a été ajoutée avec succès !');
            return $this->redirectToRoute('advert_show', [
                'slug' => $advert->getSlug(),
                'id' => $advert->getId()
            ]);
        }

        return $this->render('advert/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug}-{id}", name="advert_show", methods={"GET"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Advert $advert
     * @param String $slug
     * @return Response
     */
    public function show(Advert $advert, String $slug): Response
    {
        $newSlug = $advert->getSlug();
        if ($newSlug !== $slug){
            return $this->redirectToRoute('advert_show', [
                'id' => $advert->getId(),
                'slug' => $newSlug,
            ], 301);
        }
        return $this->render('advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }

    /**
     * @Route("/edit/{slug}-{id}", name="advert_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @Security(
     *     "is_granted('ROLE_USER') and user === advert.getAuthor()",
     *     message="Cette annonce ne vous appartient . Vous n'avez pas le droit de la modifier !"
     *     )
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

            $images = $advert->getImages();

            if ($images){
                foreach ($images as $image){
                    $image->setAdvert($advert);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre modification a été effectuée avec succès !');
            return $this->redirectToRoute('advert_show', [
                'slug' => $advert->getSlug(),
                'id' => $advert->getId()
            ]);
        }

        return $this->render('advert/edit.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="advert_delete", methods={"DELETE"})
     * @Security(
     *     "is_granted('ROLE_USER') and user === advert.getAuthor() or is_granted('ROLE_ADMIN')",
     *     message="Cette annonce ne vous appartient . Vous n'avez pas le droit de la supprimer !"
     *     )
     * @param Request $request
     * @param Advert $advert
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$advert->getId(), $request->request->get('_token'))) {
            $entityManager->remove($advert);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Votre annonce a été supprimée avec succès !');
        return $this->redirectToRoute('advert_index');
    }
}
