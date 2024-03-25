<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testIfSubmitContactFormIsSuccessfull(): void
    {
        #Création de l'environnement de test
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        // Vérifie si la réponse à la requette HTTP est réussie
        $this->assertResponseIsSuccessful();

        // Vérifie si le texte contenu dans un élément <h1> contient la chaîne 'Bienvenue sur SymRecipe'
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        // Récupérer le formulaire
        $submitButton = $crawler->selectButton('Soumettre ma demande');
        $form = $submitButton->form();

        $form["contact[fullName]"] = "Jean Dupont";
        $form["contact[email]"] = "jd@mail.com";
        $form["contact[subject]"] = "Test";
        $form["contact[message]"] = "Test";

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier le statut HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Vérifier l'envoie du mail
        $this->assertEmailCount(1);

        $client->followRedirect();

        // Vérifier la présence du message de succès
        $this->assertSelectorTextContains(
            'div.alert.alert-success.mt-4',
            'Votre demande à été envoyé avec succès !'
        );
    }
}
