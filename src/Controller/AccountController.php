<?php

namespace App\Controller;

use App\Entity\PasswordEdit;
use App\Form\AccountType;
use App\Form\PasswordEditType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 * @Security("is_granted('ROLE_USER')")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit/profile", name="account_profile_edit", methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            $this->addFlash('success',
                'Votre modification a été effectuée avec succès !'
            );
            return $this->redirectToRoute('account');
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/password", name="account_password_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editPassword(UserPasswordEncoderInterface $passwordEncoder, Request $request, EntityManagerInterface $entityManager): Response
    {
        $passwordEdit = new PasswordEdit();
        $user = $this->getUser();
        $form = $this->createForm(PasswordEditType::class, $passwordEdit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash =  $passwordEncoder->encodePassword($user, $passwordEdit->getNewPassword());
            $user->setPassword($hash);
            $entityManager->flush();

            $this->addFlash('success',
                'Votre mot de passe a été modifié avec succès. Vous pouvez vous reconnecter en toute sécutité !'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/edit_password.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/bookings", name="account_my_bookings")
     * @return Response
     */
    public function myBookings(): Response
    {
        return$this->render('account/my_bookings.html.twig', [

        ]);
    }
}
