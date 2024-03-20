<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class UserController extends AbstractController
{
    /**
     * Ce controller nous permet de modifier le profil utilisateur
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted(
        new Expression('is_granted("ROLE_USER") and user.getId() === subject.getId()'),
        subject: 'user',
    )]
    #[Route('/utilisateur/edition/{id}', name: 'user.edit' , methods: ['GET' , 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {


        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            #Ajout de la vérification du mots de passe pour l'utilisateur qui veux modifier son profil
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){
                $user = $form->getData();
                #Envoie en base (commit)
                $manager->persist($user);
                #Enregistrement en base de données(push)
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre profil à été modifier avec succès !'
                );

                return $this->redirectToRoute('recipe.index');
            } else{
                $this->addFlash(
                    'warning',
                    'Le mots de passe renseigné est incorrect!'
                );
            }


        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Ce controller nous permet de modifier le mot de passe
     * @param User $user
     * @param Request $reqest
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted(
        new Expression('is_granted("ROLE_USER") and user.getId() === subject.getId()'),
        subject: 'user',
    )]
    #[Route('/utilisateur/edition-mot-de-passe/{id}' , 'user.edit.password', methods: ['GET','POST'])]
    public function editPassword(User $user, Request $reqest, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager) : Response
    {
        #Vérification de la connexion de l'utilisateur
        if (!$this->getUser()){
            return $this->redirectToRoute('security.login');
        }
        #Vérification que ce soit le bon compte qui essaie de modifier le profil !
        if ($this->getUser() !== $user){
            return $this->redirectToRoute('recipe.index');
        }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($reqest);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setUpdatedAt(new \DateTimeImmutable());
                $user->setPlainPassword
                (
                        $form->getData()['newPassword']
                );



            $manager->persist($user);
            $manager->flush();


                $this->addFlash(
                    'success',
                    'Le mot de passe a bien été changé !'
                );
                return $this->redirectToRoute('recipe.index');
            }
            else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect!'
                );
            }
        }

        return $this->render('pages/user/edit_password.html.twig' ,[
            'form' => $form->createView()
            ]

        );
    }
}
