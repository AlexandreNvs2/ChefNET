<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact.index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $manager): Response
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

            $this->addFlash(
                'success',
                'Votre demande à été envoyé avec succès!'
            );


            #On envoie le flashMessage dans le ingredient.index
            return $this->redirectToRoute('contact.index');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
