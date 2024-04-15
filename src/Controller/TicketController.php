<?php

namespace App\Controller;
use DateTime;
use App\Entity\Ticket;
use App\Entity\Tableau;
use App\Entity\Comment;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Repository\TableauRepository;
use App\Repository\ColonneRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/test', name: 'app_ticket_test', methods: ['GET', 'POST'])]
    public function access()
    {
        return $this->render('security/acces.html.twig');
    }

    #[Route('/tableau/newTicket/{id}', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function newTicket($id,TableauRepository $tableauRepository,ColonneRepository $colonneRepository,Request $request, EntityManagerInterface $entityManager): Response
    {   
        
        $ticket = new Ticket();
        $colonnes = $colonneRepository->findByTableauId($id);
        $tableau = $tableauRepository->findOneById($id);
        $ticket->setTableau($tableau);
        $ticket->setColonne($colonnes[0]);
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();

        }
        return $this->redirectToRoute('app_tableau_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/editTicket', name: 'app_ticket_edit', methods: ['POST'])]
    public function edit(Request $request,CommentRepository $commentRepository,TableauRepository $tableauRepository,ColonneRepository $colonneRepository,TicketRepository $ticketRepository , EntityManagerInterface $entityManager): Response
    {
        $params = array();
        if($request->isXmlHttpRequest()){
            $ticketId = $request->request->get('ticketId');
            $name = $request->request->get('name');
            $description = $request->request->get('description');
            $tableauId = $request->request->get('tableauId');
            $comments = $request->request->get('comments');
            $newtxt = $request->request->get('newtxt');
            $date = $request->request->get('time');
            $ticket = $ticketRepository->findOneById($ticketId);

            if(is_Null($comments)!=1){
                $params = json_decode($comments, true);
            }
            $dataComments = $commentRepository->findByTicketId($ticketId);
            $id = (int)$tableauId;
            $ticket->setName($name);
            $ticket->setDescription($description);
            if($date!=""){
                $ticket->setDate(new \DateTime($date));
            }
            if($newtxt!=""){
                $newComment = new Comment();
                $newComment->setText($newtxt);
                $newComment->setOwner($this->getUser());
                $newComment->setDate();
                //$newComment->setTicket($ticket);
                $ticket->addComment($newComment);
                $entityManager->persist($newComment);
                $entityManager->flush();
            }
            if($params!=""){
                foreach($dataComments as $dataComment) { 
                    if($params[$dataComment->getId()] != $dataComment->getText()){
                        $dataComment->setText($params[$dataComment->getId()]);
                        $dataComment->setModifie(true);
                        $dataComment->setDate();
                    }
                    $entityManager->persist($dataComment);
                    $entityManager->flush();
                }
             }
             //var_dump($comments);
            
        }
            $entityManager->persist($ticket);
            $entityManager->flush();

            //return $this->redirectToRoute('app_ticket_test');
            return $this->redirectToRoute('app_tableau_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/tableau/deleteTicket/{id}', name: 'app_ticket_delete', methods: ['POST','GET'])]
    public function delete($id,Request $request,TableauRepository $tableauRepository,TicketRepository $ticketRepository , EntityManagerInterface $entityManager): Response
    {   

        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $ticket = $ticketRepository->findOneById($id);
            $tableau = $tableauRepository->findOneById($ticket->getTableau()->getId());
            $possible= $tableau->getOwner();
            foreach($possible as $owner){
                if($user->getId()===$owner->getId()){
                    $acces=true;
                }else{
                    $acces=false;
                }
            }
        }else{
            $acces=false;
        }
        if($acces){
             //if ($this->isCsrfTokenValid('delete'.$ticket->getId(), $request->request->get('_token'))) {
                 $entityManager->remove($ticket);
                 $entityManager->flush();
             //}
            
            return $this->redirectToRoute('app_tableau_show', ['id'=>$tableau->getId()], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }

    #[Route('/moveTicket', name: 'app_ticket_move', methods: ['POST'])]
    public function moveTicket(Request $request,
                               TableauRepository $tableauRepository,
                               TicketRepository $ticketRepository ,
                               ColonneRepository $colonneRepository, 
                               EntityManagerInterface $entityManager){  

        if($request->isXmlHttpRequest()){
            $ticketId = $request->request->get('ticketId');
            $colonneId = $request->request->get('colonneId');
            $tableauId = $request->request->get('tableauId');
            $colonneIdm = $request->request->get('colonne');
            
            $ticket = $ticketRepository->findOneById($ticketId);
            if($colonneIdm == "Déplacer ce ticket"){
                $colonne = $colonneRepository->findOneById($colonneId);
                $ticket->setColonne($colonne);
            }else{
                $colonne = $colonneRepository->findOneById($colonneIdm);
                $ticket->setColonne($colonne);
            }
        }

        $entityManager->persist($ticket);
        $entityManager->flush();

        $colonnes= $colonneRepository->findByTableauId($tableauId);
        $tickets= $ticketRepository->findByTableauId($tableauId);
        return $this->render('tableau/show.html.twig', [
            'tableau' => $tableauRepository->findOneById($tableauId), 'colonnes'=> $colonnes, 'tickets' => $tickets,
        ]);
    }
}
