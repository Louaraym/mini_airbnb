<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentAdminController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="admin_comments_index")
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    /**
     * @Route("admin/comment/edit/{id}", name="admin_comment_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Comment $comment
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {

        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**  @var Comment $comment */
            $comment = $form->getData();
            $entityManager->flush();

            $this->addFlash('success',
                "Le commentaire de : <strong>{$comment->getAuthor()}</strong> a été modifiée avec succès !"
            );
            return $this->redirectToRoute('admin_comment_edit', [
                'id' => $comment->getId()
            ]);
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }
}
