<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\TicketRepository;
use App\Repository\TableauRepository;
use App\Repository\ColonneRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/tableau/deleteComment/{id}', name: 'app_comment_delete', methods: ['POST','GET'])]
    public function delete($id,Request $request,CommentRepository $commentRepository,TableauRepository $tableauRepository,TicketRepository $ticketRepository, EntityManagerInterface $entityManager): Response
    {
        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $comment= $commentRepository->findOneById($id);
            $ticket = $ticketRepository->findOneById($comment->getTicket()->getId());
            $tableau = $tableauRepository->findOneById($ticket->getTableau()->getId());
            if($this->getUser() == $comment->getOwner()){
                $acces=true;
            }
  
        }else{
            $acces=false;
        }
        if($acces){
            //if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
                $entityManager->remove($comment);
                $entityManager->flush();
            //}

            return $this->redirectToRoute('app_tableau_show', ['id'=>$tableau->getId()], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }
}
