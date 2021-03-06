<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("adverts/booking")
 * @IsGranted("ROLE_USER")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/new/{slug}", name="booking_new", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param String $slug
     * @param Advert $advert
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, String $slug, Advert $advert, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookingType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**  @var Booking $booking */
            $booking = $form->getData();

            $booking->setAdvert($advert)
                    ->setGuest($this->getUser());

            if (!$booking->isBookableDates()){
                $this->addFlash(
                    'warning',
                    "Les dates choisis ne sont plus disponibles, veuillez changer votre choix."
                );
            }else{
                $entityManager->persist($booking);
                $entityManager->flush();

                return $this->redirectToRoute('booking_show', [
                    'id' => $booking->getId(),
                    'successAlert' => true,
                ]);
            }
        }

        return $this->render('booking/new.html.twig', [
            'advert' => $advert,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_show", methods={"GET","POST"})
     * @param Request $request
     * @param Booking $booking
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function show(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**  @var Comment $comment */
            $comment = $form->getData();

            $comment->setAdvert($booking->getAdvert())
                    ->setAuthor($this->getUser());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('advert_show', [
                'slug' => $booking->getAdvert()->getSlug(),
                'id' => $booking->getAdvert()->getId()
            ]);
        }


        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="booking_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                "La réservation N°<strong>{$booking->getId()}</strong> a été modifiée avec succès !"
            );

            return $this->redirectToRoute('booking_show', [
                'id' => $booking->getId(),
                'successAlert' => true,
            ]);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'advert' => $booking->getAdvert(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="booking_delete", methods={"DELETE"})
     * @param Request $request
     * @param Booking $booking
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Booking $booking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$booking->getId(), $request->request->get('_token'))) {
            $entityManager->remove($booking);
            $entityManager->flush();
            $this->addFlash('success',
                "La réservation de <strong>{$booking->getGuest()}</strong> a été supprimée avec succès !"
            );
        }

        return $this->redirectToRoute('advert_index');
    }
}
