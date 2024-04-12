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

    #[Route('/{id}', name: 'app_tableau_show', methods: ['GET','POST'])]
    public function show(Request $request,TableauRepository $tableauRepository,UserRepository $userRepository,TicketRepository $ticketRepository,ColonneRepository $colonneRepository, Tableau $tableau): Response
    {
        //Lowen ca bloque a cause de ces 5-6 lignes
         //$addedUserEmail ="shebeleza_1@yahoo.ca";
         //$addedUsers=$userRepository->findOneByEmail($addedUserEmail);
         //var_dump($addedUsers->getId());
        // $tableau->addOwner($addedUser);
        //         $entityManager->persist($tableau);
        //         $entityManager->flush();

        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $possible= $tableau->getOwner();
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
                //$entityManager->persist($tableau);
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
            // $addedUserEmail ="shebeleza_1@yahoo.ca"; //$request->request->get('email');
            // var_dump($addedUserEmail);
            // if($addedUserEmail && $addedUserEmail!=""){
            //     $addedUser=$userRepository->findOneByEmail($addedUserEmail);
            //     $addedUser->addTableau($tableau);
            //     var_dump($addedUser->getId());
            //     //$entityManager->persist($tableau);
            //     //$entityManager->persist($addedUser);
            //     //$entityManager->flush();
            // }
            // foreach($possible as $owner){
            //     var_dump($owner->getId());
            // }
                
            return $this->render('tableau/show.html.twig', [
                'tableaux'=>$tableaux,'tableau' => $tableau, 'colonnes'=> $colonnes, 'tickets' => $tickets, 'form'=>$form->createView(),'form2'=>$form2->createView(),
            ]);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }


    #[Route('/editTableau', name: 'app_tableau_edit', methods: ['POST'])]
    public function editTableau(Request $request,TableauRepository $tableauRepository,UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // $form = $this->createForm(TableauType::class, $comment);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_tableau_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
        // }

        // return $this->renderForm('tableau/edit.html.twig', [
        //     'tableau' => $tableau,
        //     'form' => $form,
        // ]);
        if($request->isXmlHttpRequest()){
            $nameTableau = $request->request->get('nameTableau');
            $address = $request->request->get('address');
            $tableauId = $request->request->get('tableauId');
            $tableau = $tableauRepository->findOneById($tableauId);
            var_dump($nameTableau);
            var_dump($address);
            var_dump($tableauId);
            $tableau->setName($nameTableau);
            $addedUser=$userRepository->findOneByEmail($address);
            $addedUser->addTableau($tableau);

            $entityManager->persist($addedUser);
            $entityManager->persist($tableau);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tableau_show', ['id' => $tableauId], Response::HTTP_SEE_OTHER);

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

    // #[Route('tableau/addUser', name: 'app_tableau_add_user', methods: ['POST'])]
    // public function addUser(Request $request,TableauRepository $tableauRepository,UserRepository $userRepository,EntityManagerInterface $entityManager,Tableau $tableau): Response
    // {
    //     $acces=false;
    //     if($this->getUser()){ 
    //         $user = $this->getUser();
    //         $possible= $tableau->getOwner();
            
    //         foreach($possible as $owner){
    //             if($user->getId()===$owner->getId()){
    //                 $acces=true;
    //             }
    //         }
    //     }else{
    //         $acces=false;
    //     }
    //     if($acces){
    //         $ajout = false;
    //         $addedUserEmail = $request->request->get('email');
    //         var_dump($addedUserEmail);
    //         if(!$addedUserEmail && $addedUserEmail !=""){
    //             $tableauId = $request->request->get('tableauId');
    //             $tableau = $tableauRepository->findOneById($tableauId);
    //             $addedUser=$userRepository->findOneByEmail($addedUserEmail);
    //             $owners= $tableau->getOwner();
                
    //             $tableau->addOwner($addedUser);
    //             $entityManager->persist($tableau);
    //             $entityManager->persist($addedUser);
    //             $entityManager->flush();
            
    //             // foreach($owners as $owner){
    //             //     if($user->getId()===$owner->getId()){
    //             //         $ajout=true;
    //             //     }else{
    //             //         $ajout=false;
    //             //     }
    //             // }
    //             //$tableau->$tableauRepository->addUser($addedUser);
    //         }
    //         return $this->redirectToRoute('app_tableau_show', ['id'=>$tableauId], Response::HTTP_SEE_OTHER); 
    //         //return $this->render('security/acces.html.twig');     
    //     }else{
    //         return $this->render('security/acces.html.twig');
    //     }
    // }
}
