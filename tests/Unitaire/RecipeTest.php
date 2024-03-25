<?php

namespace App\Tests\Unit;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RecipeTest extends KernelTestCase
{
    // Méthode pour créer une instance de Recipe avec des valeurs par défaut u'on réutilisera dans les test suivant
    public function getEntity(): Recipe
    {
        return (new Recipe())
            ->setName('Recipe #1')
            ->setDescription('Description #1')
            ->setIsFavorite(true)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable());
    }

    // Teste si une instance valide de Recipe passe avec succès la validation Symfony
    public function testEntityIsValid(): void
    {
        #Création de l'environnement de test
        self::bootKernel();
        $container = static::getContainer();

        // Crée une instance de Recipe avec des valeurs par défaut
        $recipe = $this->getEntity();

        // Valide l'instance de Recipe
        $errors = $container->get('validator')->validate($recipe);

        // Vérifie si aucune erreur de validation n'est renvoyée
        $this->assertCount(0, $errors);
    }

    // Teste le cas où le nom d'une recette est invalide
    public function testInvalidName()
    {
        #Création de l'environnement de test
        self::bootKernel();
        $container = static::getContainer();

        // Crée une instance de Recipe avec un nom invalide (vide)
        $recipe = $this->getEntity();
        $recipe->setName('');

        // Valide l'instance de Recipe
        $errors = $container->get('validator')->validate($recipe);

        // Vérifie si deux erreurs de validation sont renvoyées
        $this->assertCount(2, $errors);
    }

    // Teste la méthode getAverage() qui calcule la moyenne des notes pour une recette donnée
    public function testGetAverage()
    {
        // Crée une instance de Recipe
        $recipe = $this->getEntity();

        // Récupère un utilisateur associé à la recette
        $user = static::getContainer()->get('doctrine.orm.entity_manager')->find(User::class, 1);

        // Ajoute plusieurs objets Mark avec une note fixe de 2 à la recette
        for ($i = 0; $i < 5; $i++) {
            $mark = new Mark();
            $mark->setMark(2)
                ->setUser($user)
                ->setRecipe($recipe);

            $recipe->addMark($mark);
        }

        // Appelle getAverage() pour calculer la moyenne des notes
        // Vérifie si la valeur renvoyée est égale à la moyenne attendue (2.0 dans ce cas)
        $this->assertTrue(2.0 === $recipe->getAverage());
    }
}
