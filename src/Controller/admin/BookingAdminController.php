<?php

namespace App\Controller\admin;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookingAdminController
 * @package App\Controller\admin
 */
class BookingAdminController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_index", methods={"GET"})
     * @param BookingRepository $bookingRepository
     * @return Response
     */
    public function index(BookingRepository $bookingRepository): Response
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookingRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/booking/{id}/edit", name="admin_booking_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Booking $booking
     * @return Response
     */
    public function edit(Request $request, Booking $booking): Response
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $booking->setAmount(0);

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success',
                "La réservation N°<strong>{$booking->getId()}</strong> a été modifiée avec succès !"
            );

            return $this->redirectToRoute('admin_bookings_index', [
                'id' => $booking->getId(),
                'successAlert' => true,
            ]);
        }

        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'advert' => $booking->getAdvert(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/booking/delete/{id}", name="admin_booking_delete", methods={"DELETE"})
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

        return $this->redirectToRoute('admin_bookings_index');
    }
}
