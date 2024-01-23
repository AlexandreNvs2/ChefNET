<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient')]
    public function index(IngredientRepository $repository/* Injection de dépendance*/): Response
    {
        $ingredients = $repository->findAll();


        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients, //Ici on fait passer notre ingrédients en vue
        ]);
    }
}
