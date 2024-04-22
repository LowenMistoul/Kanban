<?php

namespace App\Controller;

use App\Entity\Colonne;
use App\Entity\Tableau;
use App\Form\ColonneType;
use App\Repository\ColonneRepository;
use App\Repository\TableauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/colonne')]
class ColonneController extends AbstractController
{

    #[Route('/tableau/newColonne/{id}', name: 'app_colonne_new', methods: ['GET', 'POST'])]
    /**
     * @Route("/newColonne/{id}", name="app_colonne_new")
     */
    public function newColonne($id,TableauRepository $tableauRepository,ColonneRepository $colonneRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $colonne = new Colonne();
        $position = $colonneRepository->findByTableauId($id);
        $tableau = $tableauRepository->findOneById($id);
        $colonne->setPosition(count($position)+1);
        $colonne->setTableau($tableau);
        $form = $this->createForm(ColonneType::class, $colonne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($colonne);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_tableau_show', ['id'=>$id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/editColonne', name: 'app_colonne_edit', methods: ['POST'])]
    public function edit(Request $request, ColonneRepository $colonneRepository, EntityManagerInterface $entityManager): Response
    {
        if($request->isXmlHttpRequest()){
            $colonneId = $request->request->get('colonneId');
            $name = $request->request->get('nameColonne');
            $tableauId = $request->request->get('$tableauId');
            $colonne = $colonneRepository->findOneById($colonneId);
            $id = (int)$tableauId;
            $colonne->setName($name);
            
        }

        $entityManager->persist($colonne);
        $entityManager->flush();

        return $this->redirectToRoute('app_tableau_show', ['id' => $id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/tableau/deleteColonne/{id}', name: 'app_colonne_delete', methods: ['POST','GET'])]
    public function delete(Request $request,TableauRepository $tableauRepository, Colonne $colonne, EntityManagerInterface $entityManager): Response
    {
        $acces=false;
        if($this->getUser()){ 
            $user = $this->getUser();
            $tableau = $tableauRepository->findOneById($colonne->getTableau()->getId());
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
            var_dump("in");
            //if ($this->isCsrfTokenValid('delete'.$colonne->getId(), $request->request->get('_token'))) {
                $entityManager->remove($colonne);
                $entityManager->flush();
            //}
        return $this->redirectToRoute('app_tableau_show', ['id' => $tableau->getId()], Response::HTTP_SEE_OTHER);
        }
        else {
            return $this->render('security/acces.html.twig');
        }
    }

    #[Route('/moveColonne', name: 'app_colonne_move', methods: ['POST'])]
    public function moveColonne(Request $request,
                               TableauRepository $tableauRepository,
                               ColonneRepository $colonneRepository, 
                               EntityManagerInterface $entityManager){  
        
        if($request->isXmlHttpRequest()){
            $colonneId = $request->request->get('colonneId');
            $colonneContainerId = $request->request->get('colonneContainerId');
            var_dump($colonneContainerId);
            $colonneId2 =$request->request->get('colonneId2');
            $colonneContainerId2 =$request->request->get('colonneContainerId2');
            var_dump($colonneContainerId2);
            $tableauId = $request->request->get('tableauId');
            $colonne = $colonneRepository->findOneById($colonneId);
            $colonne2 = $colonneRepository->findOneById($colonneId2);
            
            
            $colonne->setPosition($colonneContainerId);
            $colonne2->setPosition($colonneContainerId2);

        }

        $entityManager->persist($colonne);
        $entityManager->flush();
        $entityManager->persist($colonne2);
        $entityManager->flush();
        
        $colonnes= $colonneRepository->findByTableauId($tableauId);
        $tickets= $ticketRepository->findByTableauId($tableauId);
        return $this->render('tableau/show.html.twig', [
            'tableau' => $tableauRepository->findOneById($tableauId), 'colonnes'=> $colonnes, 'tickets' => $tickets,
        ]);
    }
}
