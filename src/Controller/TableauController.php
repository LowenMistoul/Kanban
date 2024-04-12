<?php

namespace App\Controller;

use Datetime;
use App\Entity\Tableau;
use App\Entity\Colonne;
use App\Entity\Ticket;
use App\Entity\Comment;
use App\Form\TableauType;
use App\Form\ColonneType;
use App\Form\TicketType;
use App\Repository\TableauRepository;
use App\Repository\UserRepository;
use App\Repository\TicketRepository;
use App\Repository\ColonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

#[Route('/tableau')]
class TableauController extends AbstractController
{
    #[Route('/tableau', name: 'app_tableau_index', methods: ['GET'])]
    public function index(TableauRepository $tableauRepository): Response
    {
        if($this->getUser()){
            $user = $this->getUser();
            $id = $user->getId();
            return $this->render('tableau/index.html.twig', [
                'tableaus' => $tableauRepository->findAllByUserID($id),
            ]);
        }else{
            return $this->render('security/acces.html.twig');
        }
    }

    #[Route('/newTableau', name: 'app_tableau_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {   
        if($this->getUser()){
            $user = $this->getUser();
            $id = $user->getId();
            $tableau = new Tableau();
            $tableau->addOwner($user);
            $form = $this->createForm(TableauType::class, $tableau);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //$link = new Link();
                //$link->setUser($this->getUser());
                //$link->setTableau($tableau);
                //$link->setOwnership("Owner");
                $entityManager->persist($tableau);
                //$entityManager->persist($link);
                $entityManager->flush();

                return $this->redirectToRoute('app_tableau_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('tableau/new.html.twig', [
                'tableau' => $tableau,
                'form' => $form,
            ]);
        }else{
            return $this->render('security/acces.html.twig');
        }
    }

    #[Route('/{id}', name: 'app_tableau_show', methods: ['GET'])]
    public function show(Request $request,TableauRepository $tableauRepository,UserRepository $userRepository,TicketRepository $ticketRepository,ColonneRepository $colonneRepository, Tableau $tableau): Response
    {
        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $possible= $tableau->getOwner();
            $addedUser=$userRepository->findOneByEmail('shebeleza_1@yahoo.ca');
            var_dump($addedUser->getId());
            foreach($possible as $owner){
                var_dump($owner->getId());
            }
            
            foreach($possible as $owner){
                if($user->getId() === $owner->getId()){
                    $acces=true;
                }
            }
        }else{
            $acces=false;
        }
        
        

        if($acces){
            $tableaux = $tableauRepository->findAllByUserID($user->getId());
            $colonnes= $colonneRepository->findByTableauId($tableau->getId());
            $tickets= $ticketRepository->findByTableauId($tableau->getId());
            $owners= $tableau->getOwner();
        
            $colonne = new Colonne();
            $position = $colonneRepository->findByTableauId($tableau->getId());
            
            $colonne->setPosition(count($position)+1);
            $colonne->setTableau($tableau);
            $form2 = $this->createForm(ColonneType::class, $colonne);
            $form2->handleRequest($request);

            
            //To create a new ticket 
            $ticketss = $tableau->getTickets();
            $ticket = new Ticket();
            
            $ticket->setTableau($tableau);
            $form = $this->createForm(TicketType::class, $ticket);
            $form->handleRequest($request);
            if(count($colonnes)>0){
                    $ticket->setColonne($colonnes[0]);
            }
                    if ($form->isSubmitted() && $form->isValid()) {
                        $entityManager->persist($newComment);
                        $entityManager->persist($ticket);
                        $entityManager->flush();

                        return $this->render('tableau/show.html.twig', [
                            'tableaux'=>$tableaux,'tableau' => $tableau, 'colonnes'=> $colonnes, 'tickets' => $tickets, 'form'=>$form->createView(),'form2'=>$form2->createView(),
                        ]);
                    }else if ($form2->isSubmitted() && $form2->isValid()) {
                        $entityManager->persist($colonne);
                        $entityManager->flush();
            
                        return $this->render('tableau/show.html.twig', [
                            'tableaux'=>$tableaux, 'tableau' => $tableau, 'colonnes'=> $colonnes, 'tickets' => $tickets, 'form'=>$form->createView(),'form2'=>$form2->createView(),
                        ]);
                    
                    }
                
                return $this->render('tableau/show.html.twig', [
                    'tableaux'=>$tableaux,'tableau' => $tableau, 'colonnes'=> $colonnes, 'tickets' => $tickets, 'form'=>$form->createView(),'form2'=>$form2->createView(),
            ]);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }


    #[Route('/{id}/edit', name: 'app_tableau_edit', methods: ['GET', 'POST'])]
    public function edit($id,Request $request, Tableau $tableau, EntityManagerInterface $entityManager): Response
    {
        return $this->redirectToRoute('app_tableau_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_tableau_delete', methods: ['POST'])]
    public function delete(Request $request, Tableau $tableau, EntityManagerInterface $entityManager): Response
    {
        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $possible= $tableau->getOwner();
            
            foreach($possible as $owner){
                if($user->getId()===$owner->getId()){
                    $acces=true;
                }
            }
        }else{
            $acces=false;
        }
    
        if($acces){
            if ($this->isCsrfTokenValid('delete'.$tableau->getId(), $request->request->get('_token'))) {
                $entityManager->remove($tableau);
                $entityManager->flush();
            }
            return $this->redirectToRoute('app_tableau_index', [], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }

    #[Route('tableau/addUser', name: 'app_tableau_add_user', methods: ['POST'])]
    public function addUser(Request $request,TableauRepository $tableauRepository,UserRepository $userRepository,EntityManagerInterface $entityManager,Tableau $tableau): Response
    {
        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $possible= $tableau->getOwner();
            
            foreach($possible as $owner){
                if($user->getId()===$owner->getId()){
                    $acces=true;
                }
            }
        }else{
            $acces=false;
        }
        if($acces){
            $ajout = false;
            $addedUserEmail = $request->request->get('email');
            var_dump($addedUserEmail);
            if(!$addedUserEmail && $addedUserEmail !=""){
                $tableauId = $request->request->get('tableauId');
                $tableau = $tableauRepository->findOneById($tableauId);
                $addedUser=$userRepository->findOneByEmail($addedUserEmail);
                $owners= $tableau->getOwner();
                
                $tableau->addOwner($addedUser);
                $entityManager->persist($tableau);
                $entityManager->flush();
            
                // foreach($owners as $owner){
                //     if($user->getId()===$owner->getId()){
                //         $ajout=true;
                //     }else{
                //         $ajout=false;
                //     }
                // }
                //$tableau->$tableauRepository->addUser($addedUser);
            }
            //return $this->redirectToRoute('app_tableau_show', ['id'=>$tableauId], Response::HTTP_SEE_OTHER); 
            return $this->render('security/acces.html.twig');     
        }else{
            return $this->render('security/acces.html.twig');
        }
    }
}
