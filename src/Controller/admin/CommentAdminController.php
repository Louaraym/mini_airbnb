<?php

namespace App\Controller\admin;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\Pagination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentAdminController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page}", name="admin_comments_index")
     * @param Pagination $pagination
     * @param int $page
     * @return Response
     */
    public function index(Pagination $pagination, $page = 1): Response
    {
        $pagination->setClassName(Comment::class)
                    ->setCurrentPage($page)
                    ->setLimit(10);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination,
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

    /**
     * @Route("admin/comment/delete/{id}", name="admin_comment_delete", methods={"DELETE"})
     * @param Request $request
     * @param Comment $comment
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
            $this->addFlash('success',
                "Le commentaire de <strong>{$comment->getAuthor()}</strong> a été supprimée avec succès !"
            );
        }

        return $this->redirectToRoute('admin_comments_index');
    }
}
