<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;


/**
 * @Route("/chemin", name="nom_route")
 * @Method({"GET", "POST"})
 * @Controller(service="App\Controller\IngredientController")
 */
class IngredientController extends AbstractController
{


    /**
     * Cette fonction affiche tout les ingrédients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */

    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    #isGranted nous permet de restreindre la Route au user avec comme role 'ROLE_USER'
    #[isGranted('ROLE_USER')]
    public function index(IngredientRepository $repository
        , PaginatorInterface                   $paginator
        , Request                              $request
        /* Injection de dépendance(Ici Paginator et Repository)*/): Response

    {

        /* Ici on paramètre notre pagination avec les query et du nombre de query par pages (ici 10 par page)*/
        $ingredients = $paginator->paginate(
        #FindBy pour afficher uniquement les ingrédients lié a l'utilisateur connecté
            $repository->findBy(['user' => $this->getUser()]),  /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10); /*limit per page*/

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients //Ici on fait passer notre ingrédients en vue
        ]);

    }

    /**
     * Ce controller montre un formulaire qui nous permet de créer un ingrédient
     * et de l'envoyer en BDD
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/creation', 'ingredient.new', methods: ['GET', 'POST'])]
    #isGranted nous permet de restreindre la Route au user avec comme role 'ROLE_USER'
    #[isGranted('ROLE_USER')]
    public function new(
        Request                $request,
        EntityManagerInterface $manager #Entity manager qui va nous permettre de push notre ingrédient en base de données  #
    ): Response
    {
        #  Création avec la classe ingrédient
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        # Si le formulaire est remplie est valide
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            #Ici lors de la création d'un ingrédient on lui injecte le User qui la créer
            $ingredient->setUser($this->getUser());
            #Envoie en base (commit)
            $manager->persist($ingredient);
            #Enregistrement en base de données(push)
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient à été ajouté avec succès !'
            );


            #On envoie le flashMessage dans le ingredient.index
            return $this->redirectToRoute('ingredient.index');
        }


        # Rendu du formulaire #
        return $this->render('pages/ingredient/new.html.twig',
            [
                'form' => $form->createView()
            ]);
    }

    /**
     * Modification d'un ingrédient et envoie en BDD
     * @param Ingredient $ingredient
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[IsGranted(
        new Expression('is_granted("ROLE_USER") and user === subject.getUser()'),
        subject: 'ingredient',
    )]
    #Ici en plus de restreindre a un compte connecté il faut aussi que l'ingrédient soit lié au user

    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            #Envoie en BDD (commit)
            $manager->persist($ingredient);
            #Enregistrement en BDD(push)
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre ingrédient à été modifié avec succès !'
            );
            #On envoie le flashMessage dans le ingredient.index
            return $this->redirectToRoute('ingredient.index');
        }


        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Suppression d'un ingrédient
     * @param EntityManagerInterface $manager
     * @param Ingredient $ingredient
     * @return Response
     */
    #[Route('/ingredient/delete/{id}', 'ingredient.delete', methods: ['GET'])]
    #[IsGranted(
        new Expression('is_granted("ROLE_USER") and user === subject.getUser()'),
        subject: 'ingredient',
    )]
    public function delete(EntityManagerInterface $manager, Ingredient $ingredient): Response
    {
        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre ingrédient à été supprimé avec succès !'
        );

        return $this->redirectToRoute('ingredient.index');
    }


}
