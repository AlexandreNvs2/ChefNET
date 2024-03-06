<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IngredientController extends AbstractController
{


    /**
     * cette fonction affiche tout les ingrédients
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient' , methods: ['GET'])]
    public function index(IngredientRepository $repository,PaginatorInterface $paginator, Request $request
        /* Injection de dépendance(Ici Paginator et Repository)*/): Response

    {

        /* Ici on paramètre notre pagination avec les query et du nombre de query par pages (ici 10 par page)*/
        $ingredients = $paginator->paginate(
            $repository->findAll(),  /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 ); /*limit per page*/

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients //Ici on fait passer notre ingrédients en vue
        ]);

    }
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'] )]
    public function new() : Response
    {
        #  Création avec la classe ingrédient   #
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        # Rendu du formulaire #
        return  $this->render('pages/ingredient/new.html.twig',
            [
            'form' => $form->createView()
        ]
        );
    }
}
