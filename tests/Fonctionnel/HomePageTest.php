<?php

namespace App\Tests\Fonctionnel;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomePageTest extends WebTestCase
{
    public function testSomething(): void
    {
        // Crée un client HTTP pour simuler une requête à l'application
        $client = static::createClient();

        // Effectue une requête GET à la page d'accueil ('/')
        $crawler = $client->request('GET', '/');

        // Vérifie si la réponse est réussie (code de statut HTTP 200)
        $this->assertResponseIsSuccessful();

        // Sélectionne un bouton avec la classe CSS '.btn.btn-primary.btn-lgz'
        // Vérifie s'il existe exactement un bouton correspondant
        $button = $crawler->filter('.btn.btn-primary.btn-lg');
        $this->assertEquals(1, count($button));

        // Vérifie si le texte contenu dans un élément <h1> contient la chaîne 'Bienvenue sur SymRecipe'
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Chef.NET');
    }
}
