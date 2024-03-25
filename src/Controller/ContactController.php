<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact;
        if ($this->getUser()) {
            $contact->setFullName($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }

        $form = $this->createForm(ContactType::class, $contact);
        # Si le formulaire est remplie est valide

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            #Envoie en base (commit)
            $manager->persist($contact);
            #Enregistrement en base de données(push)
            $manager->flush();

            //Gestion des Mails

            $email = (new TemplatedEmail())
                ->from($contact->getEmail())
                ->to('admin@Recipe.com')
                ->subject($contact->getSubject())
                ->htmlTemplate('emails/contact.html.twig')
                ->context([
                    'contact'=> $contact

                ]);


            $mailer->send($email);

            $this->addFlash(
                'success',
                'Votre demande à été envoyé avec succès !'
            );


            #On envoie le flashMessage dans le ingredient.index
            return $this->redirectToRoute('contact.index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
