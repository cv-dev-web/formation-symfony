<?php

namespace App\Controller;


use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     * 
     * @return Response
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Comment::class)
                   ->setLimit(5)
                   ->setPage($page);
        
        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
            

        ]);
    }

    /**
     * permet d'acceder au formulaire d'édition des commentaires
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     * 
     * @param Comment $comment
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function edit(Comment $comment, Request $request,EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminCommentType::class, $comment);
        $form->HandleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire numéro <strong>{$comment->getId()}</strong> a bien été modifié !"
            );

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment'=> $comment,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * 
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function delete(Comment $comment,EntityManagerInterface $manager)
    {
       
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            'success',
            "Le commentaire de {$comment->getAuthor()->getFullName()} à bien été supprimé !"
        );

        return $this->redirectToRoute('admin_comment_index');
    }
}
