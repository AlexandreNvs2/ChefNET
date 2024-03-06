<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
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
}
