<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Ce controller nous permet de se connecter
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'security.login', methods: ['POST', 'GET'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        #Au lieu d'instancier nos variable ici on les instancie dans le return
        #$lastUsername = $authenticationUtils->getLastUsername();
        #$error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/security/login.html.twig', [
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Ce controller nous permet de nous déconnecter
     * @return void
     */
    #[Route('/deconnexion', 'security.logout')]
    public function logout()
    {
        //D'après la doc symfony s'occupe du reste
    }

    /**
     * Cette fonction nous permet de créer un nouveau compte
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/inscription', 'security.registration', methods: ['POST', 'GET'])]
    public function registration(Request $request, EntityManagerInterface $manager): Response
    {
        #Création du form avec User en paramètre
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            #Envoie en base (commit)
            $manager->persist($user);
            #Enregistrement en base de données(push)
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a été créer avec succès !'
            );

            return $this->redirectToRoute('security.login');
        }


        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
