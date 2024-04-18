<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailerController extends AbstractController
{
    #[Route('/mailer-send', name: 'app_mailer', methods: ['GET', 'POST'])]
    public function sendEmail(MailerInterface $mailer):Response
    {
//         $email = (new Email())
//         ->from(new Address('mailtrap@demomailtrap.com',"Mailtrap"))
//         ->to('mistouldlowen@gmail.com')
//         //->cc('cc@example.com')
//         //->bcc('bcc@example.com')
//         //->replyTo('fabien@example.com')
//         //->priority(Email::PRIORITY_HIGH)
//         ->subject('Time for Symfony Mailer!')
//        // ->text('Sending emails is fun again!')
//         ->html('<p>See Twig integration for better HTML integration!</p>');

// //....
//   $mailer->send($email);

        // ...
      return new Response(
          'Email was sent'
       );
    }
}