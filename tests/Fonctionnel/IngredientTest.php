<?php

namespace App\Tests\Fonctionnel;

use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IngredientTest extends WebTestCase
{


    public function testIfListingredientIsSuccessful(): void
    {
        // Créer un environnement de test
        $client = static::createClient();

        // Récupère le service de génération d'URLs de Symfony.
        $urlGenerator = $client->getContainer()->get('router');

        // Récupère l'Entity Manager de Doctrine pour accéder à la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Trouve un utilisateur par son ID pour simuler une session d'utilisateur connecté.
        $user = $entityManager->find(User::class, 1);

        // Connecte l'utilisateur trouvé au client de test pour simuler une authentification.
        $client->loginUser($user);

        // Effectue une requête GET vers la route qui affiche la liste des ingrédients.
        $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.index'));

        // Vérifie que la réponse à la requête est réussie (HTTP 200-299).
        $this->assertResponseIsSuccessful();

        // Confirme que la route actuelle correspond bien à celle attendue pour l'affichage des ingrédients.
        $this->assertRouteSame('ingredient.index');
    }

    public function testIfUpdateAnIngredientIsSuccessfull(): void
    {
        // Initialise un nouvelle environnement de test.
        $client = static::createClient();

        // Accède au service de génération d'URL de Symfony.
        $urlGenerator = $client->getContainer()->get('router');

        // Obtient l'Entity Manager pour interagir avec la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Trouve un utilisateur pour simuler une session d'utilisateur connecté.
        $user = $entityManager->find(User::class, 2);

        // Trouve le premier ingrédient associé à l'utilisateur trouvé précédemment.
        $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['user' => $user]);

        // Connecte l'utilisateur trouvé au client de test.
        $client->loginUser($user);

        // Effectue une requête GET pour accéder à la page de modification de l'ingrédient.
        $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.edit', ['id' => $ingredient->getId()]));

        // Vérifie que la page se charge correctement.
        $this->assertResponseIsSuccessful();

        // Remplit le formulaire de modification avec de nouvelles valeurs pour le nom et le prix.
        $form = $crawler->filter('form[name=ingredient]')->form([
            'ingredient[name]' => "Un ingrédient 2",
            'ingredient[price]' => floatval(34)
        ]);

        // Soumet le formulaire modifié.
        $client->submit($form);

        // Vérifie que la soumission redirige correctement .
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Suit la redirection après la soumission du formulaire.
        $client->followRedirect();

        // Confirme que le message de succès est affiché sur la page de redirection.
        $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été modifié avec succès !');

        // Vérifie que la route de redirection est celle de la liste des ingrédients.
        $this->assertRouteSame('ingredient.index');
    }

        public function testIfDeleteAnIngredientIsSuccessful(): void
    {
        // Initialise un nouveau client de test pour simuler un navigateur.
        $client = static::createClient();

        // Accède au service de génération d'URL de Symfony pour créer des URLs dans l'application.
        $urlGenerator = $client->getContainer()->get('router');

        // Obtient l'Entity Manager pour interagir avec les entités et la base de données.
            $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Trouve un utilisateur spécifique pour simuler une session d'utilisateur connecté.
            $user = $entityManager->find(User::class, 2);

        // Trouve un ingrédient associé à l'utilisateur pour tester la suppression.
            $ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['user' => $user]);

        // Connecte l'utilisateur au client de test pour simuler une session authentifiée.
            $client->loginUser($user);

        // Effectue une requête GET pour déclencher la suppression de l'ingrédient.
            $crawler = $client->request(Request::METHOD_GET, $urlGenerator->generate('ingredient.delete', ['id' => $ingredient->getId()]));

        // Vérifie que la réponse indique une redirection (HTTP 302).
            $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

            // Suit la redirection pour aller à la page suivante après la suppression.
            $client->followRedirect();

        // Vérifie que le message de succès est bien affiché sur la page de destination.
            $this->assertSelectorTextContains('div.alert-success', 'Votre ingrédient a été supprimé avec succès !');

            // Confirme que la route de la page actuelle est bien celle de la liste des ingrédients après la suppression.
            $this->assertRouteSame('ingredient.index');
    }
}
