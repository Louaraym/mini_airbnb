<?php

namespace App\Controller\admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminUserController
 * @package App\Controller\admin
 * @Route("/admin")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/users/{page<\d+>?1}", name="admin_users_index")
     * @param Pagination $pagination
     * @param int $page
     * @return Response
     */
    public function index(Pagination $pagination,int $page): Response
    {
        $pagination->setClassName(User::class)
            ->setCurrentPage($page)
            ->setLimit(10);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/user/delete/{id}", name="admin_user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if (count($user->getBookings()) > 0 || count($user->getAdverts()) > 0){
            $this->addFlash('warning',
                "Vous ne pouvez pas supprimer l'utilisateur : <strong>{$user->getFullName()}</strong> 
                            Car il est lié à des annonces ou des réservations !");
        }else if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success',
                "L'utilisateur <strong>{$user->getFullName()}</strong> a été supprimé avec succès !"
            );
        }

        return $this->redirectToRoute('admin_users_index');
    }

    /**
     * @Route("/user/edit/{id}", name="admin_user_edit", methods={"GET","POST"})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(User $user,Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success',
                "Le profil de <strong>{$user->getFullName()}</strong> a été modifié avec succès !"
            );
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

}
