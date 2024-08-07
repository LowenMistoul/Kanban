<?php
namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder,EntityManagerInterface $manager)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            // 4) save the User!
            //$entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($user);
            // $entityManager->flush();
            $persist = $manager->persist($user);
            $flush = $manager->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView(),'title'=>'Sign up')
        );
    }

    /**
     * @Route("/update", name="user_update")
     */
    public function update(Request $request, UserPasswordHasherInterface $passwordEncoder,EntityManagerInterface $manager)
    {
        if($this->getUser()){
            // 1) build the form
            $user = $this->getUser();
            $form = $this->createForm(UserType::class, $user);
            $form->remove('password');
            // 2) handle the submit (will only happen on POST)
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // 3) save the User!
                // $entityManager = $this->getDoctrine()->getManager();
                // $entityManager->persist($user);
                // $entityManager->flush();
                $persist = $manager->persist($user);
                $flush = $manager->flush();

                // ... do any other work - like sending them an email, etc
                // maybe set a "flash" success message for the user

                return $this->redirectToRoute('user_update');
            }

            return $this->render(
                'security/update.html.twig',
                array('form' => $form->createView())
            );
        }else {
            return $this->render('security/acces.html.twig');
        }
    }

    /**
     * @Route("/update-password", name="user_update_password")
     */
    public function updatePassword(Request $request, UserPasswordHasherInterface $passwordEncoder,EntityManagerInterface $manager)
    {
        if($this->getUser()){
            // 1) build the form
            $user = $this->getUser();
            $form = $this->createForm(UserType::class, $user);
            $form->remove('email');
            $form->remove('userName');
            // 2) handle the submit (will only happen on POST)
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                // 3) Encode the password (you could also do this via Doctrine listener)
                $password = $passwordEncoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                // 4) save the User!
                // $entityManager = $this->getDoctrine()->getManager();
                // $entityManager->persist($user);
                // $entityManager->flush();
                $persist = $manager->persist($user);
                $flush = $manager->flush();

                // ... do any other work - like sending them an email, etc
                // maybe set a "flash" success message for the user

                return $this->redirectToRoute('user_update_password');
            }

            return $this->render(
                'security/password.html.twig',
                array('form' => $form->createView())
            );
        }else {
            return $this->render('security/acces.html.twig');
        }
    }
}