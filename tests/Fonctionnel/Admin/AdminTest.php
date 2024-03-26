<?php

namespace App\Tests\Functional\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class ContactTest extends WebTestCase
{
    public function testCrudIsHere(): void
    {
        // Crée un nouveau client de test, simulant un navigateur pour effectuer des requêtes HTTP.
        $client = static::createClient();

        // Récupère l'EntityManager de Doctrine pour accéder à la base de données.
        $entityManager = $client->getContainer()->get('doctrine.orm.entity_manager');

        // Trouve un utilisateur spécifique dans la base de données pour simuler une session d'utilisateur connecté.
        $user = $entityManager->getRepository(User::class)->findOneBy(['id' => 1]);

        // Connecte l'utilisateur trouvé au client de test pour simuler une authentification.
        $client->loginUser($user);

        // Effectue une requête GET vers la route '/admin'.
        $client->request(Request::METHOD_GET, '/admin');

        // Vérifie que la réponse à la requête est réussie .
        $this->assertResponseIsSuccessful();

        // Simule le clic sur le lien 'Demandes de contact' sur la page actuelle.
        $crawler = $client->clickLink('Demandes de contact');

        // Vérifie à nouveau que la réponse est réussie après avoir suivi le lien.
        $this->assertResponseIsSuccessful();

        // Simule le clic sur le bouton ou le lien pour créer une nouvelle 'Demande de contact'.
        $client->click($crawler->filter('.action-new')->link());

        // Vérifie que la réponse est toujours réussie après avoir tenté de créer une nouvelle demande.
        $this->assertResponseIsSuccessful();

        // Effectue une nouvelle requête GET vers la route '/admin' pour retourner à la page d'administration.
        $client->request(Request::METHOD_GET, '/admin');

        // Simule le clic sur le bouton ou le lien pour éditer une 'Demande de contact' existante.
        $client->click($crawler->filter('.action-edit')->link());

        // Vérifie que la réponse est réussie après avoir tenté d'éditer la demande.
        $this->assertResponseIsSuccessful();
    }

}
