<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'security.login',methods: ['POST' , 'GET'])]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        #Au lieu d'instancier nos variable ici on les instancie dans le return
        #$lastUsername = $authenticationUtils->getLastUsername();
        #$error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('pages/security/login.html.twig', [
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/deconnexion','security.logout')]
    public function logout()
    {
        //D'après la doc symfony s'occupe du reste
    }
}
