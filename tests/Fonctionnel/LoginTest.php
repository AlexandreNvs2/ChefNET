<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{
    public function testIfLoginIsSuccessful(): void
    {
        // Crée un client HTTP pour simuler une requête à l'application
        $client = static::createClient();

        // Récupère le générateur d'URL à partir du conteneur de services
        $urlGenerator = $client->getContainer()->get("router");

        // Effectue une requête GET à la page de connexion
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // Sélectionne le formulaire de connexion et le remplit avec des identifiants valides
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@mail.fr",
            "_password" => "password"
        ]);

        // Soumet le formulaire de connexion
        $client->submit($form);

        // Vérifie si la réponse a un code de statut HTTP 302 (Redirection)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testIfLoginFailedWhenPasswordIsWrong(): void
    {
        // Crée un client HTTP pour simuler une requête à l'application
        $client = static::createClient();

        // Récupère le générateur d'URL à partir du conteneur de services
        $urlGenerator = $client->getContainer()->get("router");

        // Effectue une requête GET à la page de connexion
        $crawler = $client->request('GET', $urlGenerator->generate('security.login'));

        // Sélectionne le formulaire de connexion et le remplit avec un mot de passe incorrect
        $form = $crawler->filter("form[name=login]")->form([
            "_username" => "admin@mail.fr",
            "_password" => "password_" // Mot de passe incorrect
        ]);

        // Soumet le formulaire de connexion
        $client->submit($form);

        // Vérifie si la réponse a un code de statut HTTP 302 (Redirection)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Suit la redirection vers la page de connexion après un échec de connexion
        $client->followRedirect();

        // Vérifie si l'utilisateur est toujours sur la page de connexion
        $this->assertRouteSame('security.login');

        // Vérifie si un message d'erreur est affiché sur la page
        $this->assertSelectorTextContains("div.alert-danger", "Invalid credentials.");
    }
}
