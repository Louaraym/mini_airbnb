<?php

namespace App\Controller;

use App\Entity\PasswordEdit;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit/profile/{id}", name="account_profile_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

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
     * @Route("/edit/password/{id}", name="account_password_edit", methods={"GET","POST"}, requirements={"slug": "[a-z0-9\-]*"})
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editPassword(UserPasswordEncoderInterface $passwordEncoder, Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
//        $user = $this->getUser();
        $passwordEdit = new PasswordEdit();

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
}
