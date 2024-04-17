<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function sendEmail(MailerInterface $mailer)
    {
      
        $email = (new Email())
        ->from('mailtrap@demomailtrap.com')
        ->to('mistouldlowen@gmail.com')
        //->cc('cc@example.com')
        //->bcc('bcc@example.com')
        //->replyTo('fabien@example.com')
        //->priority(Email::PRIORITY_HIGH)
        ->subject('Time for Symfony Mailer!')
       // ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML integration!</p>');

//....
  
  $dsn ="MAILER_DSN=smtp://api:746ddc21634ccf3443a7b163e492c6df@live.smtp.mailtrap.io:587";
  $transport=Transport::fromDsn($dsn);
  $mailer2 = new Mailer($transport);
  $mailer2->send($email);

        // ...
      return new Response(
          'Email was sent'
       );
    }
}